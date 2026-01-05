<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/RatingModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/AdminModel.php';

class HomeController extends BaseController
{
    private $gameModel;
    private $ratingModel;
    private $reviewModel;

    public function __construct()
    {
        $this->gameModel = new GameModel();
        $this->ratingModel = new RatingModel();
        $this->reviewModel = new ReviewModel();
    }

    public function index()
    {
        if (isset($_GET['__home']) && $_GET['__home'] === '1') {
            return 'home-ok';
        }

        $currentUser = $this->getCurrentUser();

        $games = $this->gameModel->getAllGamesWithRatings($this->ratingModel);
        $topRatedGames = $this->gameModel->getTopRatedGamesWithRatings(4, $this->ratingModel);

        $recommendedGames = [];
        if ($currentUser) {
            $recommendedGames = $this->gameModel->getRecommendedGamesWithRatings(
                $currentUser['id'], 
                $this->ratingModel
            );
        }

        $userId = $currentUser['id'] ?? null;
        $topReviews = $this->reviewModel->getTopReviewsByUpvotes($userId, 20);
        $topCommenters = $this->reviewModel->getTopCommenters(5);

        return $this->render('home/index', [
            'games' => $games,
            'topRatedGames' => $topRatedGames,
            'currentUser' => $currentUser,
            'recommendedGames' => $recommendedGames,
            'topReviews' => $topReviews,
            'topCommenters' => $topCommenters,
        ]);
    }

    public function filter()
    {
        $genre = isset($_GET['genre']) ? $_GET['genre'] : '';
        $platform = isset($_GET['platform']) ? $_GET['platform'] : '';

        if (!empty($genre)) {
            $games = $this->gameModel->getGamesByGenre($genre);
        } elseif (!empty($platform)) {
            $games = $this->gameModel->getGamesByPlatform($platform);
        } else {
            $games = $this->gameModel->findAll();
        }

        $games = $this->gameModel->enrichGamesWithRatings($games, $this->ratingModel);

        $currentUser = $this->getCurrentUser();

        $userId = $currentUser['id'] ?? null;
        $topReviews = $this->reviewModel->getTopReviewsByUpvotes($userId, 20);
        $topCommenters = $this->reviewModel->getTopCommenters(5);

        return $this->render('home/index', [
            'games' => $games,
            'currentUser' => $currentUser,
            'filterGenre' => $genre,
            'filterPlatform' => $platform,
            'topReviews' => $topReviews,
            'topCommenters' => $topCommenters
        ]);
    }

    public function ping()
    {
        return 'ok';
    }

    public function search()
    {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $games = [];
        $currentUser = $this->getCurrentUser();

        if (!empty($query)) {
            $games = $this->gameModel->searchGames($query);
            $games = $this->gameModel->enrichGamesWithRatings($games, $this->ratingModel);
        }

        $userId = $currentUser['id'] ?? null;
        $topReviews = $this->reviewModel->getTopReviewsByUpvotes($userId, 20);
        $topCommenters = $this->reviewModel->getTopCommenters(5);

        return $this->render('home/index', [
            'games' => $games,
            'currentUser' => $currentUser,
            'searchQuery' => $query,
            'topRatedGames' => [],
            'recommendedGames' => [],
            'topReviews' => $topReviews,
            'topCommenters' => $topCommenters
        ]);
    }

    public function searchSuggestions()
    {
        header('Content-Type: application/json');
        
        try {
            $query = isset($_GET['q']) ? trim($_GET['q']) : '';
            
            if (empty($query)) {
                echo json_encode([]);
                return;
            }
            
            $suggestions = $this->gameModel->getGameSuggestions($query, 10);
            
            foreach ($suggestions as &$suggestion) {
                if (!isset($suggestion['cover_image'])) {
                    $suggestion['cover_image'] = '';
                }
            }
            
            echo json_encode($suggestions);
        } catch (Exception $e) {
            error_log('Error in searchSuggestions: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    public function topReviews()
    {
        $currentUser = $this->getCurrentUser();
        $userId = $currentUser['id'] ?? null;
        $topReviews = $this->reviewModel->getTopReviewsByUpvotes($userId, 20);

        return $this->render('home/top-reviews', [
            'currentUser' => $currentUser,
            'topReviews' => $topReviews,
        ]);
    }

    public function about()
    {
        $currentUser = $this->getCurrentUser();
        return $this->render('home/about', [
            'currentUser' => $currentUser,
        ]);
    }

    public function topCommenters()
    {
        $currentUser = $this->getCurrentUser();
        $topCommenters = $this->reviewModel->getTopCommenters(50);

        return $this->render('home/top-commenters', [
            'currentUser' => $currentUser,
            'topCommenters' => $topCommenters,
        ]);
    }

    public function contact()
    {
        $currentUser = $this->getCurrentUser();
        
        $adminModel = new AdminModel();
        $adminEmails = $adminModel->getAdminEmails();

        return $this->render('home/contact', [
            'currentUser' => $currentUser,
            'adminEmails' => $adminEmails,
        ]);
    }
}
?>