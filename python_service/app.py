import os
import json
import requests
from flask import Flask, render_template, request, redirect, url_for, flash, Response
from models import db, Poll, Suggestion, BugReport

app = Flask(__name__)
app.config['SECRET_KEY'] = 'dev_key_123'
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@127.0.0.1/gamecritic'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db.init_app(app)

PHP_URL = "http://127.0.0.1:8000" # NOTE: If your groupmate's Apache is on port 881, they must change this to 881
EXCLUDED_HEADERS = ['content-encoding', 'content-length', 'transfer-encoding', 'connection']

@app.route('/')
def index():
    # Redirect to the main PHP site's home page
    return proxy_to_php(None)

@app.route('/create_poll', methods=['GET', 'POST'])
def create_poll():
    if request.method == 'POST':
        question = request.form.get('question')
        options_str = request.form.get('options')
        if question and options_str:
            options_list = [opt.strip() for opt in options_str.split(',') if opt.strip()]
            new_poll = Poll(question=question)
            new_poll.set_options(options_list)
            db.session.add(new_poll)
            db.session.commit()
            return redirect(url_for('vote_poll', poll_id=new_poll.id))
    return render_template('create_poll.html')

@app.route('/vote/<int:poll_id>', methods=['GET', 'POST'])
def vote_poll(poll_id):
    poll = Poll.query.get_or_404(poll_id)
    if request.method == 'POST':
        option = request.form.get('option')
        if poll.add_vote(option):
            db.session.commit()
            return redirect(url_for('poll_results', poll_id=poll.id))
    return render_template('vote_poll.html', poll=poll)

@app.route('/results/<int:poll_id>')
def poll_results(poll_id):
    poll = Poll.query.get_or_404(poll_id)
    results = poll.get_results()
    total_votes = sum(results.values())
    
    formatted_results = []
    for opt, count in results.items():
        percentage = (count / total_votes * 100) if total_votes > 0 else 0
        formatted_results.append({
            'option': opt,
            'votes': count,
            'percentage': round(percentage, 1)
        })
    
    return render_template('poll_results.html', poll=poll, results=formatted_results, total=total_votes)

@app.route('/polls')
def list_polls():
    polls = Poll.query.order_by(Poll.created_at.desc()).all()
    return render_template('list_polls.html', polls=polls)

@app.route('/suggestion', methods=['GET', 'POST'])
def suggestion():
    details = None
    from models import Game
    
    # Fetch all unique genres from DB for the dropdown
    all_genres_raw = db.session.query(Game.genre).distinct().all()
    # Flatten and clean up (split by commas if multiple genres are in one string)
    genres_set = set()
    for (g_str,) in all_genres_raw:
        if g_str:
            # Split by comma or slash if they exist
            parts = [p.strip() for p in g_str.replace('/', ',').split(',')]
            genres_set.update(parts)
    
    available_genres = sorted(list(genres_set))

    if request.method == 'POST':
        mode = request.form.get('mode', 'category')
        genre = request.form.get('genre')
        mood = request.form.get('mood')
        
        query = Game.query
        reason = ""
        
        if mode == 'mood' and mood:
            # Mood to Genre Mapping
            mood_map = {
                'Happy': ['Adventure', 'Platformer', 'Casual', 'Racing'],
                'Sad': ['Indie', 'Story', 'Atmospheric', 'Relaxing'],
                'Angry': ['Action', 'Shooter', 'Fighting', 'Slash'],
                'Chill': ['Simulation', 'Puzzle', 'Strategy', 'Card'],
                'Adventurous': ['RPG', 'Open World', 'Exploration', 'Fantasy']
            }
            target_genres = mood_map.get(mood, [])
            if target_genres:
                from sqlalchemy import or_
                filters = [Game.genre.like(f'%{tg}%') for tg in target_genres]
                query = query.filter(or_(*filters))
            reason = f"Since you're feeling {mood}, we thought these might match your energy."
        else:
            # Normal Category Mode
            if genre and genre != 'All':
                query = query.filter(Game.genre.like(f'%{genre}%'))
            reason = f"Based on the community games list, we found these {genre if genre and genre != 'All' else 'top'} titles for you."
        
        suggested_games_objs = query.limit(5).all()
        suggested_games = [{'id': g.id, 'title': g.title} for g in suggested_games_objs]
        
        if not suggested_games:
            suggested_games = [{'id': 0, 'title': 'No matches found in your library yet.'}]
            
        details = {'games': suggested_games, 'reason': reason, 'genre': genre, 'mood': mood, 'mode': mode}
        
    return render_template('suggestion.html', details=details, available_genres=available_genres)

@app.route('/submit_suggestion', methods=['POST'])
def submit_suggestion():
    games_json = request.form.get('games_json')
    reason = request.form.get('reason')
    if games_json and reason:
        games = json.loads(games_json)
        new_sug = Suggestion(reason=reason)
        # Store just the titles for back-compat or objects? Let's keep titles in the list for simple display
        game_titles = [g['title'] if isinstance(g, dict) else g for g in games]
        new_sug.set_game_list(game_titles)
        db.session.add(new_sug)
        db.session.commit()
        flash("Suggestion saved to your profile!")
    return redirect(url_for('suggestion'))

@app.route('/report_bug', methods=['GET', 'POST'])
def report_bug():
    if request.method == 'POST':
        bug_type = request.form.get('bug_type')
        name = request.form.get('name')
        fix_details = request.form.get('fix_details')
        
        if bug_type and name and fix_details:
            new_report = BugReport(
                bug_type=bug_type,
                reporter_name=name,
                fix_details=fix_details
            )
            db.session.add(new_report)
            db.session.commit()
            flash("Bug report submitted successfully! Thank you for your help.")
            return redirect(url_for('report_bug'))
            
    return render_template('report_bug.html')

@app.route('/bugs')
def view_bugs():
    bugs = BugReport.query.order_by(BugReport.created_at.desc()).all()
    return render_template('view_bugs.html', bugs=bugs)

@app.errorhandler(404)
def proxy_to_php(e=None):
    path = request.path
    query = request.query_string.decode('utf-8')
    
    # 1. Handle the prefix stripping for requests coming from the browser
    # The PHP code often generates /Gamecritic/public/ paths
    processed_path = path
    if processed_path.startswith('/Gamecritic/public'):
        processed_path = processed_path[len('/Gamecritic/public'):]
    
    # Ensure processed_path is at least /
    if not processed_path:
        processed_path = '/'
        
    full_url = f"{PHP_URL}{processed_path}"
    if query:
        full_url += f"?{query}"

    headers = {key: value for (key, value) in request.headers if key.lower() != 'host'}
    data = request.get_data()
    
    try:
        resp = requests.request(
            method=request.method,
            url=full_url,
            headers=headers,
            data=data,
            cookies=request.cookies,
            allow_redirects=False)

        resp_headers = []
        for name, value in resp.raw.headers.items():
            if name.lower() in EXCLUDED_HEADERS:
                continue
            
            # Rewrite Location header to keep user on port 5000
            if name.lower() == 'location':
                new_value = value.replace('http://localhost:8000', '')
                new_value = new_value.replace('http://127.0.0.1:8000', '')
                new_value = new_value.replace('/Gamecritic/public', '')
                if not new_value.startswith('http'):
                    # Ensure it's relative to root
                    if not new_value.startswith('/'):
                        new_value = '/' + new_value
                value = new_value
            
            # Rewrite Set-Cookie path to ensure sessions work across the proxy
            if name.lower() == 'set-cookie':
                value = value.replace('Path=/Gamecritic/public', 'Path=/')
                value = value.replace('path=/Gamecritic/public', 'path=/')
            
            resp_headers.append((name, value))

        # 2. Fix images and links in the HTML response
        content = resp.content
        if 'text/html' in resp.headers.get('Content-Type', '').lower():
            text_content = resp.text
            # Replace absolute PHP URLs with relative ones that hit our proxy
            text_content = text_content.replace('http://localhost:8000/Gamecritic/public', '/Gamecritic/public')
            text_content = text_content.replace('http://localhost:8000', '')
            text_content = text_content.replace('http://127.0.0.1:8000/Gamecritic/public', '/Gamecritic/public')
            text_content = text_content.replace('http://127.0.0.1:8000', '')
            
            # Injection logic: Add our features back into the navigation and homepage
            # without modifying the source files.
            
            # Carousel Links (already handled natively in PHP)
            pass

            # Inject Navbar links
            nav_injection = """
                <li class="nav-item"><a class="nav-link" href="/create_poll"><i class="fas fa-poll me-1"></i>Polls</a></li>
                <li class="nav-item"><a class="nav-link" href="/suggestion"><i class="fas fa-lightbulb me-1"></i>Suggestions</a></li>
                <li class="nav-item"><a class="nav-link" href="/report_bug"><i class="fas fa-bug me-1"></i>Report Bug</a></li>
            """
            text_content = text_content.replace('</ul>', nav_injection + '</ul>', 1)
            
            # Inject Feature Cards on homepage
            if path == '/' or processed_path == '/':
                # Inject before the Featured Games section (already in PHP views)
                # text_content = text_content.replace('<!-- Featured Games -->', card_injection + '<!-- Featured Games -->')
                pass
            
            content = text_content.encode('utf-8')

        return Response(content, resp.status_code, resp_headers)
    except requests.exceptions.ConnectionError:
        return f"Error: PHP Server ({PHP_URL}) is not running.", 502

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    app.run(debug=True, port=5000)
