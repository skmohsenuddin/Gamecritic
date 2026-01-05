<?php
require_once __DIR__ . '/BaseModel.php';

class ReviewModel extends BaseModel {
    protected $table = 'reviews';

    public function getReviewsForGame(int $gameId): array {
        $stmt = $this->db->prepare("SELECT r.*, u.username FROM {$this->table} r LEFT JOIN users u ON u.id = r.user_id WHERE r.game_id = ? ORDER BY r.created_at DESC");
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function addReview(int $gameId, int $userId, float $rating, string $review = null): bool {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (game_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iids', $gameId, $userId, $rating, $review);
        return $stmt->execute();
    }

    public function deleteReview(int $reviewId, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? AND user_id = ?");
        if (!$stmt) return false;
        $stmt->bind_param('ii', $reviewId, $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getCommentsForGameSorted(int $gameId, ?int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*,
                    u.username,
                    rv.vote_type AS user_vote
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN review_votes rv
            ON rv.review_id = r.id
            AND rv.user_id = ?
            WHERE r.game_id = ?
            AND r.comment IS NOT NULL
            AND r.comment != ''
            ORDER BY r.vote_score DESC, r.created_at DESC"
        );

        $uid = $userId ?? 0; 
        $gid = $gameId;

        $stmt->bind_param("ii", $uid, $gid);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getReviewsFromFollowedUsers(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, 
                    u.username,
                    u.profile_picture,
                    g.title as game_title,
                    g.cover_image,
                    g.id as game_id,
                    rv.vote_type AS user_vote
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             JOIN games g ON r.game_id = g.id
             JOIN followers f ON f.following_id = r.user_id AND f.follower_id = ?
             LEFT JOIN review_votes rv ON rv.review_id = r.id AND rv.user_id = ?
             WHERE r.comment IS NOT NULL 
               AND r.comment != ''
             ORDER BY r.created_at DESC
             LIMIT 50"
        );
        $stmt->bind_param('ii', $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopReviewsByUpvotes(?int $userId = null, int $limit = 20): array {
        $uid = $userId ?? 0;
        $stmt = $this->db->prepare(
            "SELECT r.*,
                    u.username,
                    u.profile_picture,
                    g.title as game_title,
                    g.cover_image,
                    g.id as game_id,
                    rv.vote_type AS user_vote
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             JOIN games g ON r.game_id = g.id
             LEFT JOIN review_votes rv ON rv.review_id = r.id AND rv.user_id = ?
             WHERE r.comment IS NOT NULL 
               AND r.comment != ''
             ORDER BY r.upvotes DESC, r.downvotes ASC, r.created_at DESC
             LIMIT ?"
        );
        $stmt->bind_param('ii', $uid, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopCommenters(int $limit = 3): array {
        $stmt = $this->db->prepare(
            "SELECT u.id,
                    u.username,
                    u.profile_picture,
                    COUNT(r.id) as total_comments
             FROM users u
             INNER JOIN reviews r ON r.user_id = u.id
             WHERE r.comment IS NOT NULL 
               AND r.comment != ''
             GROUP BY u.id, u.username, u.profile_picture
             ORDER BY total_comments DESC
             LIMIT ?"
        );
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}
?>
