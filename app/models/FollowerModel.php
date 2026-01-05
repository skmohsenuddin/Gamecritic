<?php
require_once __DIR__ . '/BaseModel.php';

class FollowerModel extends BaseModel {
    protected $table = 'followers';

    public function follow(int $followerId, int $followingId): bool {
        if ($followerId === $followingId) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (follower_id, following_id) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE follower_id = follower_id"
        );
        $stmt->bind_param('ii', $followerId, $followingId);
        return $stmt->execute();
    }

    public function unfollow(int $followerId, int $followingId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} 
             WHERE follower_id = ? AND following_id = ?"
        );
        $stmt->bind_param('ii', $followerId, $followingId);
        return $stmt->execute();
    }

    public function isFollowing(int $followerId, int $followingId): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM {$this->table} 
             WHERE follower_id = ? AND following_id = ?"
        );
        $stmt->bind_param('ii', $followerId, $followingId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getFollowing(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.username, u.profile_picture, f.created_at
             FROM {$this->table} f
             JOIN users u ON f.following_id = u.id
             WHERE f.follower_id = ?
             ORDER BY f.created_at DESC"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getFollowers(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.username, u.profile_picture, f.created_at
             FROM {$this->table} f
             JOIN users u ON f.follower_id = u.id
             WHERE f.following_id = ?
             ORDER BY f.created_at DESC"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

