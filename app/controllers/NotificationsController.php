<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/FollowerModel.php';

class NotificationsController extends BaseController {
    private $notificationModel;
    private $followerModel;

    public function __construct() {
        $this->notificationModel = new NotificationModel();
        $this->followerModel = new FollowerModel();
        $this->requireLogin();
    }

    public function index() {
        $this->ensureSessionStarted();
        $userId = $_SESSION['user_id'];

        $notifications = $this->notificationModel->getUserNotifications($userId);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        foreach ($notifications as &$notification) {
            if ($notification['type'] === 'new_follower' && !empty($notification['link'])) {
                if (preg_match('/user_id=(\d+)/', $notification['link'], $matches)) {
                    $followerUserId = (int)$matches[1];
                    $notification['follower_user_id'] = $followerUserId;
                    $notification['is_following_back'] = $this->followerModel->isFollowing($userId, $followerUserId);
                }
            }
        }
        unset($notification);

        return $this->render('notifications/index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function markRead() {
        $this->ensureSessionStarted();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid method']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $notificationId = (int)($_POST['notification_id'] ?? 0);

        if ($notificationId > 0) {
            $success = $this->notificationModel->markAsRead($notificationId, $userId);
            $this->jsonResponse(['success' => $success]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid notification ID']);
        }
    }

    public function markAllRead() {
        $this->ensureSessionStarted();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid method']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $success = $this->notificationModel->markAllAsRead($userId);
        $this->jsonResponse(['success' => $success]);
    }

    public function getUnreadCount() {
        $this->ensureSessionStarted();
        
        if (!$this->isLoggedIn()) {
            $this->jsonResponse(['count' => 0]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $count = $this->notificationModel->getUnreadCount($userId);
        $this->jsonResponse(['count' => $count]);
    }

    public function delete() {
        $this->ensureSessionStarted();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid method']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $notificationId = (int)($_POST['notification_id'] ?? 0);

        if ($notificationId > 0) {
            $success = $this->notificationModel->deleteNotification($notificationId, $userId);
            $this->jsonResponse(['success' => $success]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid notification ID']);
        }
    }
}

