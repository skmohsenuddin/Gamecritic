<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/RatingModel.php';

class HomeController extends BaseController
{
    private $gameModel;
    private $ratingModel;

    public function __construct()
    {
        $this->gameModel = new GameModel();
        $this->ratingModel = new RatingModel();
    }

    public function index()
    {
        if (isset($_GET['__home']) && $_GET['__home'] === '1') {
            return 'home-ok';
        }
        $games = $this->gameModel->findAll();
        $topRatedGames = $this->gameModel->getTopRatedGames(4);
        $currentUser = $this->getCurrentUser();

        // Attach ratings to games
        foreach ([$games, $topRatedGames] as &$gameList) {
            foreach ($gameList as &$game) {
                $game['ratings'] = $this->ratingModel->getGameRatings((int) $game['id']);
            }
        }

        // Get personalized recommended games for "Things You May Like" section
        $recommendedGames = [];
        if ($currentUser) {
            // If user is logged in, get personalized recommendations based on their reviews
            $recommendedGames = $this->gameModel->getRecommendedGames($currentUser['id']);
            foreach ($recommendedGames as &$game) {
                $game['ratings'] = $this->ratingModel->getGameRatings((int) $game['id']);
            }
        } else {
            // If not logged in, show empty array (will display "Start exploring" message)
            $recommendedGames = [];
        }

        return $this->render('home/index', [
            'games' => $games,
            'topRatedGames' => $topRatedGames,
            'currentUser' => $currentUser,
            'recommendedGames' => $recommendedGames,
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

        $currentUser = $this->getCurrentUser();

        return $this->render('home/index', [
            'games' => $games,
            'currentUser' => $currentUser,
            'filterGenre' => $genre,
            'filterPlatform' => $platform
        ]);
    }

    public function ping()
    {
        return 'ok';
    }
}
?>