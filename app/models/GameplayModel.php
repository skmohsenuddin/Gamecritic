<?php
require_once __DIR__ . '/BaseModel.php';

class GameplayModel extends BaseModel {
    protected $table = 'gameplays';

    public function getAllGameplays(int $limit = 50): array {
        try {
            $stmt = $this->db->prepare(
                "SELECT g.*, 
                        u.username,
                        u.profile_picture,
                        gm.title as game_title,
                        gm.cover_image,
                        gm.id as game_id
                 FROM {$this->table} g
                 JOIN users u ON g.user_id = u.id
                 JOIN games gm ON g.game_id = gm.id
                 ORDER BY g.created_at DESC
                 LIMIT ?"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('i', $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching gameplays: " . $e->getMessage());
            return [];
        }
    }

    public function getGameplayById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT g.*, 
                    u.username,
                    u.profile_picture,
                    gm.title as game_title,
                    gm.cover_image,
                    gm.id as game_id
             FROM {$this->table} g
             JOIN users u ON g.user_id = u.id
             JOIN games gm ON g.game_id = gm.id
             WHERE g.id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function getGameplaysByGame(int $gameId): array {
        $stmt = $this->db->prepare(
            "SELECT g.*, 
                    u.username,
                    u.profile_picture
             FROM {$this->table} g
             JOIN users u ON g.user_id = u.id
             WHERE g.game_id = ?
             ORDER BY g.created_at DESC"
        );
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserGameplays(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT g.*, 
                    gm.title as game_title,
                    gm.cover_image
             FROM {$this->table} g
             JOIN games gm ON g.game_id = gm.id
             WHERE g.user_id = ?
             ORDER BY g.created_at DESC"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createGameplay(int $userId, int $gameId, string $title, string $videoPath, ?string $description = null): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (user_id, game_id, title, video_path, description) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('iisss', $userId, $gameId, $title, $videoPath, $description);
        return $stmt->execute();
    }

    public function deleteGameplay(int $gameplayId, int $userId): bool {
        $gameplay = $this->getGameplayById($gameplayId);
        if (!$gameplay || (int)$gameplay['user_id'] !== $userId) {
            return false;
        }

        if (!empty($gameplay['video_path'])) {
            $videoPath = __DIR__ . '/../../public' . $gameplay['video_path'];
            if (file_exists($videoPath)) {
                unlink($videoPath);
            }
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $gameplayId, $userId);
        return $stmt->execute();
    }
}

