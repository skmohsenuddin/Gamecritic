<?php
require_once __DIR__ . '/BaseModel.php';

class GameModel extends BaseModel {
    protected $table = 'games';

    public function getGameById($id) {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getTopRatedGames($limit = 10) {
        $query = "SELECT * FROM {$this->table} ORDER BY RAND() LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getGamesByGenre($genre) {
        $query = "SELECT * FROM {$this->table} WHERE genre = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $genre);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getGamesByPlatform($platform) {
        $query = "SELECT * FROM {$this->table} WHERE platform = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $platform);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createGame($data) {
        // Match current DB schema
        $query = "INSERT INTO {$this->table} (title, genre, platform, release_year, cover_image, description) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssiss", 
            $data['title'],
            $data['genre'], 
            $data['platform'], 
            $data['release_year'], 
            $data['cover_image'],
            $data['description']
        );
        return $stmt->execute();
    }

    public function updateGame($id, $data) {
        // Match current DB schema
        $query = "UPDATE {$this->table} SET title = ?, genre = ?, platform = ?, 
                  release_year = ?, cover_image = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssissi", 
            $data['title'],
            $data['genre'], 
            $data['platform'], 
            $data['release_year'], 
            $data['cover_image'],
            $data['description'],
            $id
        );
        return $stmt->execute();
    }

    public function getTotalGames() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalReviews() {
        $query = "SELECT COUNT(*) as total FROM reviews";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getUserReviewCount($userId) {
        $query = "SELECT COUNT(*) as count FROM reviews WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    public function getUserFavoriteGenre($userId) {
        $query = "SELECT g.genre, COUNT(*) as review_count 
                  FROM reviews r 
                  JOIN games g ON r.game_id = g.id 
                  WHERE r.user_id = ? 
                  GROUP BY g.genre 
                  ORDER BY review_count DESC 
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['genre'] : 'N/A';
    }

    public function getUserTopGenres($userId, $limit = 3) {
        $query = "SELECT g.genre, COUNT(*) as review_count 
                  FROM reviews r 
                  JOIN games g ON r.game_id = g.id 
                  WHERE r.user_id = ? 
                  GROUP BY g.genre 
                  ORDER BY review_count DESC 
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserReviews($userId) {
        $query = "SELECT g.*, r.rating, r.comment, r.created_at as review_date 
                  FROM reviews r 
                  JOIN games g ON r.game_id = g.id 
                  WHERE r.user_id = ? 
                  ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRecommendedGames($userId) {
        // Get user's top 3 genres
        $topGenres = $this->getUserTopGenres($userId, 3);
        
        if (empty($topGenres)) {
            // If no reviews, return empty array (will show "Start Exploring" message)
            return [];
        }
        
        // Extract genre names
        $genres = array_column($topGenres, 'genre');
        
        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count($genres) - 1) . '?';
        
        // Get games from user's top genres, mixing them up
        $query = "SELECT * FROM games WHERE genre IN ($placeholders) ORDER BY RAND() LIMIT 6";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(str_repeat('s', count($genres)), ...$genres);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMostInteractedGenre() {
        // Get the genre with the most total interactions (votes + reviews)
        $query = "SELECT g.genre, 
                         (COUNT(DISTINCT r.id) + COUNT(DISTINCT CASE WHEN r.rating >= 0.5 THEN r.id END)) as total_interactions
                  FROM games g 
                  LEFT JOIN reviews r ON g.id = r.game_id 
                  WHERE g.genre IS NOT NULL AND g.genre != ''
                  GROUP BY g.genre 
                  ORDER BY total_interactions DESC 
                  LIMIT 1";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row ? $row['genre'] : 'N/A';
    }

    public function getRecommendedGamesByMostInteracted() {
        // Get the most interacted genre
        $mostInteractedGenre = $this->getMostInteractedGenre();
        
        if ($mostInteractedGenre === 'N/A') {
            // If no interactions, return random games
            $query = "SELECT * FROM games ORDER BY RAND() LIMIT 6";
            $result = $this->db->query($query);
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get games from the most interacted genre
        $query = "SELECT * FROM games WHERE genre = ? ORDER BY RAND() LIMIT 6";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $mostInteractedGenre);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserReviewedGames($userId) {
        // Get games that the user has reviewed
        $query = "SELECT DISTINCT g.*, r.rating, r.comment, r.created_at as review_date
                  FROM games g 
                  INNER JOIN reviews r ON g.id = r.game_id 
                  WHERE r.user_id = ? 
                  ORDER BY r.created_at DESC 
                  LIMIT 6";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function appendCommentToGame(int $gameId, string $username, string $comment): bool {
        $entry = sprintf("[%s] %s: %s\n", date('Y-m-d H:i'), $username === '' ? 'User' : $username, $comment);
        $sql = "UPDATE {$this->table} SET comments = CONCAT(COALESCE(comments, ''), ?) WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('si', $entry, $gameId);
        return $stmt->execute();
    }
}
?>



