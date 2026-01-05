<?php
require_once __DIR__ . '/BaseModel.php';

class MessageModel extends BaseModel {
    protected $table = 'messages';

    public function sendMessage(int $senderId, int $receiverId, string $message): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM followers WHERE follower_id = ? AND following_id = ?"
        );
        if (!$stmt) {
            error_log("MessageModel: Failed to prepare followers check query: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param('ii', $senderId, $receiverId);
        if (!$stmt->execute()) {
            error_log("MessageModel: Failed to execute followers check: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows === 0) {
            error_log("MessageModel: User {$senderId} is not following user {$receiverId}");
            return false; // Not following, can't send message
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (sender_id, receiver_id, message) VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            error_log("MessageModel: Failed to prepare insert query: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param('iis', $senderId, $receiverId, $message);
        $success = $stmt->execute();
        
        if (!$success) {
            error_log("MessageModel: Failed to insert message: " . $stmt->error);
        }
        
        $stmt->close();
        return $success;
    }

    public function getConversation(int $userId1, int $userId2, int $limit = 50): array {
        $stmt = $this->db->prepare(
            "SELECT m.*, 
                    u1.username as sender_username,
                    u1.profile_picture as sender_profile_picture,
                    u2.username as receiver_username,
                    u2.profile_picture as receiver_profile_picture
             FROM {$this->table} m
             JOIN users u1 ON m.sender_id = u1.id
             JOIN users u2 ON m.receiver_id = u2.id
             WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                OR (m.sender_id = ? AND m.receiver_id = ?)
             ORDER BY m.created_at ASC
             LIMIT ?"
        );
        $stmt->bind_param('iiiii', $userId1, $userId2, $userId2, $userId1, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getNewMessages(int $userId, int $otherUserId, ?int $lastMessageId = null): array {
        if ($lastMessageId === null) {
            return $this->getConversation($userId, $otherUserId);
        }

        $stmt = $this->db->prepare(
            "SELECT m.*, 
                    u1.username as sender_username,
                    u1.profile_picture as sender_profile_picture,
                    u2.username as receiver_username,
                    u2.profile_picture as receiver_profile_picture
             FROM {$this->table} m
             JOIN users u1 ON m.sender_id = u1.id
             JOIN users u2 ON m.receiver_id = u2.id
             WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
                OR (m.sender_id = ? AND m.receiver_id = ?))
             AND m.id > ?
             ORDER BY m.created_at ASC"
        );
        $stmt->bind_param('iiiii', $userId, $otherUserId, $otherUserId, $userId, $lastMessageId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function markAsRead(int $userId, int $otherUserId): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET is_read = 1 
             WHERE receiver_id = ? AND sender_id = ? AND is_read = 0"
        );
        $stmt->bind_param('ii', $userId, $otherUserId);
        return $stmt->execute();
    }

    public function getUnreadCount(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM {$this->table} 
             WHERE receiver_id = ? AND is_read = 0"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)($result['count'] ?? 0);
    }

    public function getConversationList(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT 
                    CASE 
                        WHEN sender_id = ? THEN receiver_id
                        ELSE sender_id
                    END as other_user_id
             FROM {$this->table}
             WHERE sender_id = ? OR receiver_id = ?"
        );
        $stmt->bind_param('iii', $userId, $userId, $userId);
        $stmt->execute();
        $partners = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $conversations = [];
        foreach ($partners as $partner) {
            $otherUserId = (int)$partner['other_user_id'];
            
            $userStmt = $this->db->prepare("SELECT id, username, profile_picture FROM users WHERE id = ?");
            $userStmt->bind_param('i', $otherUserId);
            $userStmt->execute();
            $user = $userStmt->get_result()->fetch_assoc();
            
            if (!$user) continue;
            
            $lastMsgStmt = $this->db->prepare(
                "SELECT message, created_at FROM {$this->table}
                 WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                 ORDER BY created_at DESC LIMIT 1"
            );
            $lastMsgStmt->bind_param('iiii', $userId, $otherUserId, $otherUserId, $userId);
            $lastMsgStmt->execute();
            $lastMessage = $lastMsgStmt->get_result()->fetch_assoc();
            
            $unreadStmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM {$this->table}
                 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0"
            );
            $unreadStmt->bind_param('ii', $userId, $otherUserId);
            $unreadStmt->execute();
            $unread = $unreadStmt->get_result()->fetch_assoc();
            
            $conversations[] = [
                'other_user_id' => $otherUserId,
                'username' => $user['username'],
                'profile_picture' => $user['profile_picture'],
                'last_message' => $lastMessage['message'] ?? null,
                'last_message_time' => $lastMessage['created_at'] ?? null,
                'unread_count' => (int)($unread['count'] ?? 0)
            ];
        }
        
        usort($conversations, function($a, $b) {
            $timeA = $a['last_message_time'] ? strtotime($a['last_message_time']) : 0;
            $timeB = $b['last_message_time'] ? strtotime($b['last_message_time']) : 0;
            return $timeB - $timeA;
        });
        
        return $conversations;
    }
}
?>

