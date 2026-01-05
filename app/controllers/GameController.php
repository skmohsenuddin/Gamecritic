<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/ReviewVoteModel.php';
require_once __DIR__ . '/../models/RatingModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/FollowerModel.php';

class GameController extends BaseController {
    private $baseUrl;
    private $gameModel;
    private $reviewModel;
    private $reviewVoteModel;
    protected $ratingModel;
    private $notificationModel;
    private $userModel;
    private $followerModel;


    public function __construct() {
        $this->baseUrl = $this->baseUrl();
        $this->gameModel = new GameModel();
        $this->reviewModel = new ReviewModel();
        $this->ratingModel = new RatingModel();
        $this->reviewVoteModel = new ReviewVoteModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->followerModel = new FollowerModel();
    }

    public function show($id) {
        $game = $this->gameModel->getGameById((int)$id);
        if (!$game) {
            http_response_code(404);
            return 'Game not found';
        }

        $cover = $game['cover_image'] ?? '';
        if ($cover !== '') {
            if (strpos($cover, '/images/') === 0) {
                $game['cover_resolved'] = $this->baseUrl() . $cover;
            } elseif (strpos($cover, 'images/') === 0) {
                $game['cover_resolved'] = $this->baseUrl() . '/' . $cover;
            } else {
                $game['cover_resolved'] = $cover;
            }
        } else {
            $game['cover_resolved'] = $this->baseUrl() . '/images/default.jpg';
        }

        $ratings = $this->ratingModel->getGameRatings((int)$id);

        $userRatingAvg = null;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        if (!empty($_SESSION['user_id'])) {
            $userRating = $this->ratingModel->getUserRating(
                (int)$game['id'],
                (int)$_SESSION['user_id']
            );

            if ($userRating) {
                $userRatingAvg = array_sum($userRating) / count($userRating);
            }
        }

        $reviews = $this->reviewModel->getCommentsForGameSorted((int)$id, $userId);
        
        if ($userId) {
            foreach ($reviews as &$review) {
                if (isset($review['user_id'])) {
                    $review['is_following'] = $this->followerModel->isFollowing((int)$userId, (int)$review['user_id']);
                }
            }
            unset($review);
        }
        
        $hasRated = false;
        if ($userId) {
            $hasRated = $this->ratingModel->hasUserRatedGame($userId, $id);
        }

        return $this->render('game/show', [
            'game' => $game,
            'currentUser' => $this->getCurrentUser(),
            'reviews' => $reviews,
            'ratings' => $ratings,
            'hasRated' => $hasRated,
            'userRatingAvg' => $userRatingAvg,
        ]);
    }

    public function review($id) {

        $this->ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return 'Method Not Allowed';
        }

        if (!$this->isLoggedIn() || !isset($_SESSION['user_id'])) {
            $this->redirect($this->baseUrl . '/login?redirected=1&reason=comment');
            exit;
        }

        $review = trim($_POST['review'] ?? '');
        if ($review === '') {
            $_SESSION['error'] = 'Review cannot be empty';
            $this->redirect($this->baseUrl . '/game/' . (int)$id);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $rating = 0.0;

        $ok = $this->reviewModel->addReview((int)$id, $userId, $rating, $review);

        if ($ok) {
            $game = $this->gameModel->getGameById((int)$id);
            $user = $this->userModel->findById($userId);
            $gameTitle = $game['title'] ?? 'Unknown Game';
            $reviewerName = $user['username'] ?? 'Someone';
            
            $this->notificationModel->notifyNewReview((int)$id, $userId, $gameTitle, $reviewerName);
            
            $this->notificationModel->notifyFollowersReview((int)$id, $userId, $gameTitle, $reviewerName);
            
            $this->redirect($this->baseUrl . '/game/' . (int)$id);
            exit;
        }

        http_response_code(500);
        return 'Unable to save review';
    }

    public function vote() {
        $this->ensureSessionStarted();

        if (!$this->isLoggedIn()) {
            $back = urlencode($_SERVER['HTTP_REFERER'] ?? $this->baseUrl);
            $this->redirect($this->baseUrl . '/login?redirected=1&reason=vote&back=' . $back);
            exit;
        }

        if (!isset($_POST['review_id'], $_POST['vote'])) {
            $this->redirect($_SERVER['HTTP_REFERER'] ?? $this->baseUrl);
            exit;
        }

        $reviewId = (int) $_POST['review_id'];
        $vote     = (int) $_POST['vote'];
        $userId   = $_SESSION['user_id'];
        $voteType = $vote === 1 ? 'up' : 'down';

        try {
            $this->reviewVoteModel->vote($reviewId, $userId, $voteType);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? $this->baseUrl);
        exit;
    }

    public function rate($id) {
        $this->ensureSessionStarted();

        if (!$this->isLoggedIn()) {
            $this->redirect($this->baseUrl . '/login?redirected=1&reason=rating');
            exit;
        }

        $userId = $_SESSION['user_id'];

        $game = $this->gameModel->getGameById($id); 
        
        return $this->render('game/rate', [
            'gameId' => $id,
            'game' => $game
        ]);
    }

    public function submitRate($id) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        if (!$userId) {
            $_SESSION['rating_error'] = "You must be logged in to submit a rating.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $gameId = (int)$id;

        $ratings = [
            'fun' => isset($_POST['fun']) ? (int)$_POST['fun'] : null,
            'graphics' => isset($_POST['graphics']) ? (int)$_POST['graphics'] : null,
            'audio' => isset($_POST['audio']) ? (int)$_POST['audio'] : null,
            'story' => isset($_POST['story']) ? (int)$_POST['story'] : null,
            'ux_ui' => isset($_POST['ux_ui']) ? (int)$_POST['ux_ui'] : null,
            'technical' => isset($_POST['technical']) ? (int)$_POST['technical'] : null,
        ];

        foreach ($ratings as $key => $value) {
            if ($value === null) {
                $_SESSION['rating_error'] = "Please select a rating for all categories.";
                $_SESSION['rating_values'] = $_POST; 
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }

        $this->ratingModel->saveRating(    
            $gameId,
            $userId,
            $ratings['fun'],
            $ratings['graphics'],
            $ratings['audio'],
            $ratings['story'],
            $ratings['ux_ui'],
            $ratings['technical']);

        $this->redirect($this->baseUrl . '/game/' . (int)$id . '#ratings');
    }

}
?>