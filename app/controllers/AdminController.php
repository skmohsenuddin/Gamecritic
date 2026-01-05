<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class AdminController extends BaseController {
    private $gameModel;
    private $userModel;
    private $notificationModel;

    public function __construct() {
        $this->gameModel = new GameModel();
        $this->userModel = new UserModel();
        $this->notificationModel = new NotificationModel();
        $this->requireAdmin();
    }

    public function dashboard() {
        $games = $this->gameModel->findAll();
        $currentUser = $this->getCurrentUser();
        
        return $this->render('admin/dashboard', [
            'games' => $games,
            'currentUser' => $currentUser
        ]);
    }

    public function addGame() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $coverImage = null;
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/images/';
                $fileExtension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
                        $coverImage = $fileName;
                    }
                }
            }

            $gameData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'genre' => $_POST['genre'] ?? '',
                'platform' => $_POST['platform'] ?? '',
                'release_year' => $_POST['release_year'] ?? '',
                'cover_image' => $coverImage ? '/images/' . $coverImage : '/images/default.jpg'
            ];

            $gameId = $this->gameModel->createGame($gameData);
            if ($gameId) {
                $this->notificationModel->notifyNewGame($gameId, $gameData['title']);
                $this->redirect('/admin/dashboard?success=game_added');
            } else {
                $this->redirect('/admin/add-game?error=creation_failed');
            }
        }

        return $this->render('admin/add-game', [
            'formData' => $_POST ?? [],
            'error' => $_GET['error'] ?? null,
            'success' => $_GET['success'] ?? null
        ]);
    }

    public function editGame($id) {
        $game = $this->gameModel->findById($id);
        
        if (!$game) {
            $this->redirect('/admin/dashboard?error=game_not_found');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $coverImage = $game['cover_image'];
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/images/';
                $fileExtension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = uniqid() . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
                        if ($game['cover_image'] && file_exists($uploadDir . basename($game['cover_image']))) {
                            unlink($uploadDir . basename($game['cover_image']));
                        }
                        $coverImage = $fileName;
                    }
                }
            }

            $finalCoverImage = '/images/default.jpg';
            
            if ($coverImage && $coverImage !== $game['cover_image']) {
                $finalCoverImage = '/images/' . $coverImage;
            } elseif ($game['cover_image']) {
                $finalCoverImage = $game['cover_image'];
            }
            
            $gameData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'genre' => $_POST['genre'] ?? '',
                'platform' => $_POST['platform'] ?? '',
                'release_year' => $_POST['release_year'] ?? '',
                'cover_image' => $finalCoverImage
            ];

            if ($this->gameModel->updateGame($id, $gameData)) {
                $this->redirect('/admin/dashboard?success=game_updated');
            } else {
                $this->redirect("/admin/edit-game/{$id}?error=update_failed");
            }
        }

        return $this->render('admin/edit-game', [
            'game' => $game,
            'error' => $_GET['error'] ?? null,
            'success' => $_GET['success'] ?? null
        ]);
    }

    public function deleteGame($id) {
        if ($this->gameModel->delete($id)) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to delete game']);
        }
    }

}
?>



