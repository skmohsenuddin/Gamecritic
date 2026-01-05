<?php
require_once __DIR__ . '/BaseModel.php';

class BugReportModel extends BaseModel {
    protected $table = 'bug_report';

    public function getAllBugReports(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createBugReport(string $bugType, string $reporterName, string $fixDetails): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (bug_type, reporter_name, fix_details) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('sss', $bugType, $reporterName, $fixDetails);
        return $stmt->execute();
    }
}
?>


