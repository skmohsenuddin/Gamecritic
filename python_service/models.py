from flask_sqlalchemy import SQLAlchemy
from datetime import datetime
import json

db = SQLAlchemy()

class Poll(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    question = db.Column(db.String(255), nullable=False)
    # Storing options as a JSON string for simplicity in SQLite: ["Option A", "Option B"]
    options = db.Column(db.Text, nullable=False)
    # Storing results as a JSON string: {"Option A": 0, "Option B": 0}
    result = db.Column(db.Text, nullable=False, default='{}')
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def set_options(self, options_list):
        self.options = json.dumps(options_list)
        # Initialize results with 0 votes for each option
        initial_results = {opt: 0 for opt in options_list}
        self.result = json.dumps(initial_results)

    def get_options(self):
        return json.loads(self.options)

    def get_results(self):
        return json.loads(self.result)

    def add_vote(self, option):
        results = self.get_results()
        if option in results:
            results[option] += 1
            self.result = json.dumps(results)
            return True
        return False

class Suggestion(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    game_list = db.Column(db.Text, nullable=False) # JSON or comma-separated list of suggested games
    generated_date = db.Column(db.DateTime, default=datetime.utcnow)
    reason = db.Column(db.Text, nullable=True) # Reason for suggestion

    def set_game_list(self, games):
        self.game_list = json.dumps(games)

    def get_game_list(self):
        try:
            return json.loads(self.game_list)
        except:
            return self.game_list

class BugReport(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    bug_type = db.Column(db.String(100), nullable=False)
    reporter_name = db.Column(db.String(100), nullable=False)
    fix_details = db.Column(db.Text, nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

class Game(db.Model):
    __tablename__ = 'games'
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255))
    genre = db.Column(db.String(100))
    platform = db.Column(db.String(100))
    release_year = db.Column(db.Integer)
    cover_image = db.Column(db.String(255))
    description = db.Column(db.Text)
