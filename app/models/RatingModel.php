<?php
require_once __DIR__ . '/BaseModel.php';

class RatingModel extends BaseModel {

    protected $table = 'ratings';

    public function getGameRatings(int $gameId): array {
        $stmt = $this->db->prepare("
            SELECT
                ROUND(AVG(value),1) AS overall,
                ROUND(AVG(fun),1) AS fun,
                ROUND(AVG(graphics),1) AS graphics,
                ROUND(AVG(audio),1) AS audio,
                ROUND(AVG(story),1) AS story,
                ROUND(AVG(ux_ui),1) AS ux_ui,
                ROUND(AVG(technical),1) AS technical,
                COUNT(*) AS total_votes
            FROM {$this->table}
            WHERE game_id = ?
        ");
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?? [];
    }

    public function hasUserRatedGame(int $userId, int $gameId): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM {$this->table} WHERE user_id = ? AND game_id = ? LIMIT 1");
        if (!$stmt) return false;

        $stmt->bind_param('ii', $userId, $gameId);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    public function saveRating(
        int $gameId,
        int $userId,
        int $fun,
        int $graphics,
        int $audio,
        int $story,
        int $ux_ui,
        int $technical
    ): bool {

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $_SESSION['rating_error'] = "You must be logged in to submit a rating.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
        $fun       = max(1, min(5, $fun));
        $graphics  = max(1, min(5, $graphics));
        $audio     = max(1, min(5, $audio));
        $story     = max(1, min(5, $story));
        $ux_ui     = max(1, min(5, $ux_ui));
        $technical = max(1, min(5, $technical));

        $value = round(($fun + $graphics + $audio + $story + $ux_ui + $technical) / 6, 1);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (game_id, user_id, fun, graphics, audio, story, ux_ui, technical, value)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                fun = VALUES(fun),
                graphics = VALUES(graphics),
                audio = VALUES(audio),
                story = VALUES(story),
                ux_ui = VALUES(ux_ui),
                technical = VALUES(technical),
                value = VALUES(value)
        ");

        $stmt->bind_param(
            'iiiiiiiid',
            $gameId,
            $userId,
            $fun,
            $graphics,
            $audio,
            $story,
            $ux_ui,
            $technical,
            $value
        );

        return $stmt->execute();
    }

    public function getUserRating(int $gameId, int $userId) {
        $stmt = $this->db->prepare("
            SELECT fun, graphics, audio, story, ux_ui, technical
            FROM ratings
            WHERE game_id = ? AND user_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $gameId, $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function hasRated(int $gameId, int $userId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE game_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $gameId, $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

}