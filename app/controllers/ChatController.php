<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/FollowerModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ChatController extends BaseController {
    private $messageModel;
    private $followerModel;
    private $userModel;

    public function __construct() {
        $this->messageModel = new MessageModel();
        $this->followerModel = new FollowerModel();
        $this->userModel = new UserModel();
    }

    public function index() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId = (int)$_SESSION['user_id'];
        $conversations = $this->messageModel->getConversationList($userId);
        $following = $this->followerModel->getFollowing($userId);

        return $this->render('chat/index', [
            'currentUser' => $this->getCurrentUser(),
            'conversations' => $conversations,
            'following' => $following
        ]);
    }

    public function getConversation() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $otherUserId = (int)($_GET['user_id'] ?? 0);

        if ($otherUserId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        if (!$this->followerModel->isFollowing($userId, $otherUserId)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'You can only chat with users you follow']);
            return;
        }

        $messages = $this->messageModel->getConversation($userId, $otherUserId);
        $otherUser = $this->userModel->findById($otherUserId);

        $this->messageModel->markAsRead($userId, $otherUserId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'otherUser' => $otherUser
        ]);
    }

    public function getNewMessages() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $otherUserId = (int)($_GET['user_id'] ?? 0);
        $lastMessageId = isset($_GET['last_message_id']) ? (int)$_GET['last_message_id'] : null;

        if ($otherUserId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        $messages = $this->messageModel->getNewMessages($userId, $otherUserId, $lastMessageId);
        
        if (!empty($messages)) {
            $this->messageModel->markAsRead($userId, $otherUserId);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function sendMessage() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $senderId = (int)$_SESSION['user_id'];
        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if ($receiverId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid receiver ID']);
            return;
        }

        if (empty($message)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
            return;
        }

        if (!$this->followerModel->isFollowing($senderId, $receiverId)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'You can only send messages to users you follow. Please follow this user first.'
            ]);
            return;
        }

        if ($this->messageModel->sendMessage($senderId, $receiverId, $message)) {
            $conversation = $this->messageModel->getConversation($senderId, $receiverId, 1);
            $sentMessage = !empty($conversation) ? end($conversation) : null;

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $sentMessage
            ]);
        } else {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to send message. Please try again or contact support if the problem persists.'
            ]);
        }
    }

    public function getUnreadCount() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'count' => 0]);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $count = $this->messageModel->getUnreadCount($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
    }
}
?>

