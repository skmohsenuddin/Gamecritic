<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';

class GameController extends BaseController {
    private $gameModel;
    private $reviewModel;

    public function __construct() {
        $this->gameModel = new GameModel();
        $this->reviewModel = new ReviewModel();
    }

    public function show($id) {
        $game = $this->gameModel->findById((int)$id);
        if (!$game) {
            http_response_code(404);
            return 'Game not found';
        }

        // Normalize cover image path
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

        $reviews = $this->reviewModel->getCommentsForGameSorted((int)$id);

        return $this->render('game/show', [
            'game' => $game,
            'currentUser' => $this->getCurrentUser(),
            'reviews' => $reviews,
        ]);
    }

    public function review($id) {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        // Comments do not influence score; store neutral rating
        $ratingStore = 0.0;
        $comment = trim($_POST['comment'] ?? '');
        $ok = $this->reviewModel->addReview((int)$id, (int)$_SESSION['user_id'], $ratingStore, $comment);
        if ($ok) {
            // Also append to games.comments log field
            $username = $_SESSION['user_name'] ?? '';
            $safeComment = substr($comment, 0, 2000);
            $this->gameModel->appendCommentToGame((int)$id, $username, $safeComment);
            $this->redirect('/game/' . (int)$id);
        } else {
            http_response_code(400);
            return 'Unable to save review';
        }
    }

}
?>


