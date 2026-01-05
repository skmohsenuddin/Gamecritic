<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PollModel.php';
require_once __DIR__ . '/../models/SuggestionModel.php';
require_once __DIR__ . '/../models/BugReportModel.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/GameplayModel.php';

class CommunityController extends BaseController {
    private $pollModel;
    private $suggestionModel;
    private $bugReportModel;
    private $gameModel;
    private $gameplayModel;
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pollModel = new PollModel();
        $this->suggestionModel = new SuggestionModel();
        $this->bugReportModel = new BugReportModel();
        $this->gameModel = new GameModel();
        $this->gameplayModel = new GameplayModel();
    }

    public function polls() {
        $polls = $this->pollModel->getAllPolls();
        return $this->render('community/polls', [
            'currentUser' => $this->getCurrentUser(),
            'polls' => $polls
        ]);
    }

    public function votePoll($id) {
        $this->ensureSessionStarted();
        $poll = $this->pollModel->getPollById((int)$id);
        if (!$poll) {
            $this->redirect('/polls');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = 'Please login to vote on polls';
                $this->redirect('/poll/' . (int)$id . '/vote');
            }

            $option = trim($_POST['option'] ?? '');

            if (empty($option)) {
                $_SESSION['error'] = 'Please select an option';
                $this->redirect('/poll/' . (int)$id . '/vote');
            }

            if ($this->pollModel->vote((int)$id, $option)) {
                $_SESSION['success'] = 'Vote submitted successfully!';
                $this->redirect('/poll/' . (int)$id . '/results');
            } else {
                $_SESSION['error'] = 'Failed to submit vote';
                $this->redirect('/poll/' . (int)$id . '/vote');
            }
        }

        return $this->render('community/vote_poll', [
            'currentUser' => $this->getCurrentUser(),
            'poll' => $poll
        ]);
    }

    public function pollResults($id) {
        $poll = $this->pollModel->getPollById((int)$id);
        if (!$poll) {
            $this->redirect('/polls');
        }

        return $this->render('community/poll_results', [
            'currentUser' => $this->getCurrentUser(),
            'poll' => $poll
        ]);
    }

    public function suggestion() {
        $this->ensureSessionStarted();
        $details = null;
        $availableGenres = [];
        $stmt = $this->db->prepare("SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL AND genre != ''");
        $stmt->execute();
        $result = $stmt->get_result();
        $genresSet = [];
        while ($row = $result->fetch_assoc()) {
            if (!empty($row['genre'])) {
                $genreParts = preg_split('/[,\/]/', $row['genre']);
                foreach ($genreParts as $part) {
                    $trimmed = trim($part);
                    if (!empty($trimmed)) {
                        $genresSet[$trimmed] = true;
                    }
                }
            }
        }
        $availableGenres = array_keys($genresSet);
        sort($availableGenres);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mode = $_POST['mode'] ?? 'category';
            $genre = $_POST['genre'] ?? '';
            $mood = $_POST['mood'] ?? '';

            $suggestedGames = [];

            if ($mode === 'mood' && $mood) {
                $moodMap = [
                    'Happy' => ['Adventure', 'Platformer', 'Casual', 'Racing'],
                    'Sad' => ['Indie', 'Story', 'Atmospheric', 'Relaxing'],
                    'Angry' => ['Action', 'Shooter', 'Fighting', 'Slash'],
                    'Chill' => ['Simulation', 'Puzzle', 'Strategy', 'Card'],
                    'Adventurous' => ['RPG', 'Open World', 'Exploration', 'Fantasy']
                ];
                $targetGenres = $moodMap[$mood] ?? [];
                
                if (!empty($targetGenres)) {
                    $query = "SELECT * FROM games WHERE ";
                    $conditions = [];
                    foreach ($targetGenres as $tg) {
                        $conditions[] = "genre LIKE ?";
                    }
                    $query .= implode(' OR ', $conditions) . " LIMIT 5";
                    
                    $stmt = $this->db->prepare($query);
                    $types = str_repeat('s', count($targetGenres));
                    $params = array_map(function($tg) { return "%{$tg}%"; }, $targetGenres);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $suggestedGames = $result->fetch_all(MYSQLI_ASSOC);
                }
                $reason = "Since you're feeling {$mood}, we thought these might match your energy.";
            } else {
                if ($genre && $genre !== 'All') {
                    $stmt = $this->db->prepare("SELECT * FROM games WHERE genre LIKE ? LIMIT 5");
                    $genreParam = "%{$genre}%";
                    $stmt->bind_param('s', $genreParam);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $suggestedGames = $result->fetch_all(MYSQLI_ASSOC);
                } else {
                    $suggestedGames = $this->gameModel->getTopRatedGames(5);
                }
                $reason = "Based on the community games list, we found these " . ($genre && $genre !== 'All' ? $genre : 'top') . " titles for you.";
            }

            if (empty($suggestedGames)) {
                $suggestedGames = [['id' => 0, 'title' => 'No matches found in your library yet.']];
            }

            $details = [
                'games' => $suggestedGames,
                'reason' => $reason,
                'genre' => $genre,
                'mood' => $mood,
                'mode' => $mode
            ];
        }

        return $this->render('community/suggestion', [
            'currentUser' => $this->getCurrentUser(),
            'details' => $details,
            'availableGenres' => $availableGenres
        ]);
    }

    public function submitSuggestion() {
        $this->ensureSessionStarted();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/suggestion');
        }

        $gamesJson = $_POST['games_json'] ?? '';
        $reason = $_POST['reason'] ?? '';

        if (!empty($gamesJson) && !empty($reason)) {
            $games = json_decode($gamesJson, true);
            if ($games && $this->suggestionModel->createSuggestion($games, $reason)) {
                $_SESSION['success'] = 'Suggestion saved to your profile!';
            } else {
                $_SESSION['error'] = 'Failed to save suggestion';
            }
        }

        $this->redirect('/suggestion');
    }

    public function reportBug() {
        $this->ensureSessionStarted();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bugType = trim($_POST['bug_type'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $fixDetails = trim($_POST['fix_details'] ?? '');

            if ($bugType && $name && $fixDetails) {
                if ($this->bugReportModel->createBugReport($bugType, $name, $fixDetails)) {
                    $_SESSION['success'] = 'Bug report submitted successfully! Thank you for your help.';
                } else {
                    $_SESSION['error'] = 'Failed to submit bug report';
                }
                $this->redirect('/report_bug');
            } else {
                $_SESSION['error'] = 'Please fill in all fields';
            }
        }

        return $this->render('community/report_bug', [
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function viewBugs() {
        $this->ensureSessionStarted();
        $this->requireAdmin();

        $bugs = $this->bugReportModel->getAllBugReports();

        return $this->render('community/view_bugs', [
            'currentUser' => $this->getCurrentUser(),
            'bugs' => $bugs
        ]);
    }

    public function gameplays() {
        $this->ensureSessionStarted();
        $gameplays = $this->gameplayModel->getAllGameplays();
        $games = $this->gameModel->findAll();

        return $this->render('community/gameplays', [
            'currentUser' => $this->getCurrentUser(),
            'gameplays' => $gameplays,
            'games' => $games
        ]);
    }

    public function uploadGameplay() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to upload gameplay';
            $this->redirect('/gameplays');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/gameplays');
        }

        $gameId = (int)($_POST['game_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$gameId || !$title) {
            $_SESSION['error'] = 'Please select a game and provide a title';
            $this->redirect('/gameplays');
        }

        $videoPath = null;
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/gameplays/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileInfo = pathinfo($_FILES['video']['name']);
            $extension = strtolower($fileInfo['extension']);
            
            $allowedTypes = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
            if (!in_array($extension, $allowedTypes)) {
                $_SESSION['error'] = 'Only MP4, WebM, OGG, MOV, and AVI files are allowed.';
                $this->redirect('/gameplays');
            }

            $maxSize = 100 * 1024 * 1024;
            if ($_FILES['video']['size'] > $maxSize) {
                $_SESSION['error'] = 'Video file size must be less than 100MB.';
                $this->redirect('/gameplays');
            }

            $filename = 'gameplay_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath)) {
                $videoPath = '/uploads/gameplays/' . $filename;
            } else {
                $_SESSION['error'] = 'Failed to upload video file.';
                $this->redirect('/gameplays');
            }
        } else {
            $_SESSION['error'] = 'Please select a video file to upload.';
            $this->redirect('/gameplays');
        }

        if ($this->gameplayModel->createGameplay($_SESSION['user_id'], $gameId, $title, $videoPath, $description ?: null)) {
            $_SESSION['success'] = 'Gameplay uploaded successfully!';
        } else {
            $_SESSION['error'] = 'Failed to upload gameplay.';
        }

        $this->redirect('/gameplays');
    }

    public function viewGameplay($id) {
        $gameplay = $this->gameplayModel->getGameplayById((int)$id);
        if (!$gameplay) {
            $this->redirect('/gameplays');
        }

        return $this->render('community/view_gameplay', [
            'currentUser' => $this->getCurrentUser(),
            'gameplay' => $gameplay
        ]);
    }

    public function deleteGameplay($id) {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to delete gameplay';
            $this->redirect('/gameplays');
        }

        if ($this->gameplayModel->deleteGameplay((int)$id, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Gameplay deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete gameplay or you do not have permission.';
        }

        $this->redirect('/gameplays');
    }
}
?>
