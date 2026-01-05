<?php
require_once __DIR__ . '/BaseModel.php';

class SuggestionModel extends BaseModel {
    protected $table = 'suggestion';

    public function getAllSuggestions(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY generated_date DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $suggestions = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($suggestions as &$suggestion) {
            $suggestion['game_list'] = json_decode($suggestion['game_list'], true) ?? [];
        }
        unset($suggestion);
        
        return $suggestions;
    }

    public function createSuggestion(array $gameList, ?string $reason = null): bool {
        $gameListJson = json_encode($gameList);
        
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (game_list, reason) VALUES (?, ?)"
        );
        $stmt->bind_param('ss', $gameListJson, $reason);
        return $stmt->execute();
    }
}
?>


