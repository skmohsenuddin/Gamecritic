<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/FollowerModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class UserController extends BaseController {
    private $userModel;
    private $gameModel;
    private $followerModel;
    private $reviewModel;
    private $notificationModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->gameModel = new GameModel();
        $this->followerModel = new FollowerModel();
        $this->reviewModel = new ReviewModel();
        $this->notificationModel = new NotificationModel();
    }

    public function dashboard() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            $this->redirect('/login');
        }

        $reviewedGames = $this->gameModel->getUserReviewedGames($user['id']);
        
        $userReviews = $this->gameModel->getUserReviewCount($user['id']);
        
        $favoriteGenre = $this->gameModel->getUserFavoriteGenre($user['id']);

        return $this->render('user/dashboard', [
            'user' => $user,
            'currentUser' => $this->getCurrentUser(),
            'reviewedGames' => $reviewedGames,
            'userReviews' => $userReviews,
            'favoriteGenre' => $favoriteGenre
        ]);
    }

    public function profile() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            $this->redirect('/login');
        }

        return $this->render('user/profile', [
            'user' => $user,
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function updateProfile() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        $userId = (int)$_SESSION['user_id'];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        if (empty($username) || empty($email)) {
            $_SESSION['error'] = 'Username and email are required.';
            $this->redirect('/profile');
        }

        $existingUser = $this->userModel->findByUsernameOrEmail($username, $email, $userId);
        if ($existingUser) {
            $_SESSION['error'] = 'Username or email already exists.';
            $this->redirect('/profile');
        }

        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileInfo = pathinfo($_FILES['profile_picture']['name']);
            $extension = strtolower($fileInfo['extension']);
            
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($extension, $allowedTypes)) {
                $_SESSION['error'] = 'Only JPG, PNG, and GIF files are allowed.';
                $this->redirect('/profile');
            }

            $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                $profilePicture = '/uploads/profiles/' . $filename;
            }
        }

        $updateData = [
            'username' => $username,
            'email' => $email,
            'phone' => $phone
        ];

        if ($profilePicture) {
            $updateData['profile_picture'] = $profilePicture;
        }

        if ($this->userModel->updateUser($userId, $updateData)) {
            if (!empty($currentPassword) && !empty($newPassword)) {
                if ($this->userModel->changePassword($userId, $currentPassword, $newPassword)) {
                    $_SESSION['success'] = 'Profile and password updated successfully.';
                } else {
                    $_SESSION['error'] = 'Current password is incorrect.';
                }
            } else {
                $_SESSION['success'] = 'Profile updated successfully.';
            }

            $_SESSION['user_name'] = $username;
            $_SESSION['user_email'] = $email;
        } else {
            $_SESSION['error'] = 'Failed to update profile.';
        }

        $this->redirect('/profile');
    }

    public function myReviews() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            $this->redirect('/login');
        }

        $reviews = $this->gameModel->getUserReviews($user['id']);

        return $this->render('user/my-reviews', [
            'user' => $user,
            'currentUser' => $this->getCurrentUser(),
            'reviews' => $reviews
        ]);
    }

    public function recommendations() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            $this->redirect('/login');
        }

        $recommendedGames = $this->gameModel->getRecommendedGames($user['id']);

        return $this->render('user/recommendations', [
            'user' => $user,
            'currentUser' => $this->getCurrentUser(),
            'recommendedGames' => $recommendedGames
        ]);
    }

    public function follow() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please login to follow users']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $followerId = (int)$_SESSION['user_id'];
        $followingId = (int)($_POST['user_id'] ?? 0);

        if ($followingId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        if ($this->followerModel->follow($followerId, $followingId)) {
            $follower = $this->userModel->findById($followerId);
            $followingUser = $this->userModel->findById($followingId);
            
            if ($follower && $followingUser) {
                $followerName = $follower['username'] ?? 'Someone';
                $this->notificationModel->createNotification(
                    $followingId,
                    'new_follower',
                    'New Follower',
                    "{$followerName} started following you",
                    '/chat?user_id=' . $followerId
                );
            }
            
            echo json_encode(['success' => true, 'message' => 'User followed successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Failed to follow user']);
        }
    }

    public function unfollow() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please login to unfollow users']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $followerId = (int)$_SESSION['user_id'];
        $followingId = (int)($_POST['user_id'] ?? 0);

        if ($followingId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        if ($this->followerModel->unfollow($followerId, $followingId)) {
            echo json_encode(['success' => true, 'message' => 'User unfollowed successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Failed to unfollow user']);
        }
    }

    public function followedReviews() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            $this->redirect('/login');
        }

        $reviews = $this->reviewModel->getReviewsFromFollowedUsers((int)$_SESSION['user_id']);

        return $this->render('user/followed-reviews', [
            'user' => $user,
            'currentUser' => $this->getCurrentUser(),
            'reviews' => $reviews
        ]);
    }

    public function followers() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId = (int)$_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->redirect('/login');
        }

        $followers = $this->followerModel->getFollowers($userId);
        
        foreach ($followers as &$follower) {
            $follower['is_following_back'] = $this->followerModel->isFollowing($userId, $follower['id']);
        }
        unset($follower);

        $following = $this->followerModel->getFollowing($userId);

        return $this->render('user/followers', [
            'user' => $user,
            'currentUser' => $this->getCurrentUser(),
            'followers' => $followers,
            'following' => $following
        ]);
    }
}
?>
