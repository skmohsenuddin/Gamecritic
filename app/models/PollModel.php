<?php
require_once __DIR__ . '/BaseModel.php';

class PollModel extends BaseModel {
    protected $table = 'poll';

    public function getAllPolls(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $polls = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($polls as &$poll) {
            $poll['options'] = json_decode($poll['options'], true) ?? [];
            $poll['result'] = json_decode($poll['result'], true) ?? [];
        }
        unset($poll);
        
        return $polls;
    }

    public function getPollById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $poll = $result->fetch_assoc();
        
        if ($poll) {
            $poll['options'] = json_decode($poll['options'], true) ?? [];
            $poll['result'] = json_decode($poll['result'], true) ?? [];
        }
        
        return $poll;
    }

    public function createPoll(string $question, array $options): bool {
        $optionsJson = json_encode($options);
        $resultJson = json_encode(array_fill_keys($options, 0));
        
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (question, options, result) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('sss', $question, $optionsJson, $resultJson);
        return $stmt->execute();
    }

    public function vote(int $pollId, string $option): bool {
        $poll = $this->getPollById($pollId);
        if (!$poll || !in_array($option, $poll['options'])) {
            return false;
        }
        
        $results = $poll['result'];
        if (isset($results[$option])) {
            $results[$option]++;
        } else {
            $results[$option] = 1;
        }
        
        $resultJson = json_encode($results);
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET result = ? WHERE id = ?"
        );
        $stmt->bind_param('si', $resultJson, $pollId);
        return $stmt->execute();
    }
}
?>


