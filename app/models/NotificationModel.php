<?php
require_once __DIR__ . '/BaseModel.php';

class NotificationModel extends BaseModel {
    protected $table = 'notifications';

    public function createNotification(int $userId, string $type, string $title, string $message, ?string $link = null): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (user_id, type, title, message, link) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('issss', $userId, $type, $title, $message, $link);
        return $stmt->execute();
    }

    public function getUserNotifications(int $userId, int $limit = 50): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?"
        );
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUnreadCount(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM {$this->table} 
             WHERE user_id = ? AND is_read = 0"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)($result['count'] ?? 0);
    }

    public function markAsRead(int $notificationId, int $userId): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET is_read = 1 
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param('ii', $notificationId, $userId);
        return $stmt->execute();
    }

    public function markAllAsRead(int $userId): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET is_read = 1 
             WHERE user_id = ? AND is_read = 0"
        );
        $stmt->bind_param('i', $userId);
        return $stmt->execute();
    }

    public function deleteNotification(int $notificationId, int $userId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} 
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param('ii', $notificationId, $userId);
        return $stmt->execute();
    }

    public function notifyNewReview(int $gameId, int $reviewerId, string $gameTitle, string $reviewerName): void {
        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE id != ?"
        );
        $stmt->bind_param('i', $reviewerId);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $title = "New Review";
        $message = "{$reviewerName} posted a new review for {$gameTitle}";
        $link = "/game/{$gameId}";

        foreach ($users as $user) {
            $this->createNotification(
                (int)$user['id'],
                'new_review',
                $title,
                $message,
                $link
            );
        }
    }

    public function notifyFollowersReview(int $gameId, int $reviewerId, string $gameTitle, string $reviewerName): void {
        $stmt = $this->db->prepare(
            "SELECT follower_id FROM followers WHERE following_id = ?"
        );
        $stmt->bind_param('i', $reviewerId);
        $stmt->execute();
        $followers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $title = "Followed Reviewer Activity";
        $message = "{$reviewerName} posted a new review for {$gameTitle}";
        $link = "/game/{$gameId}";

        foreach ($followers as $follower) {
            $this->createNotification(
                (int)$follower['follower_id'],
                'followed_reviewer_review',
                $title,
                $message,
                $link
            );
        }
    }

    public function notifyNewGame(int $gameId, string $gameTitle): void {
        $stmt = $this->db->prepare("SELECT id FROM users");
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $title = "New Game Released";
        $message = "A new game '{$gameTitle}' has been added to GameCritic!";
        $link = "/game/{$gameId}";

        foreach ($users as $user) {
            $this->createNotification(
                (int)$user['id'],
                'new_game',
                $title,
                $message,
                $link
            );
        }
    }
}

