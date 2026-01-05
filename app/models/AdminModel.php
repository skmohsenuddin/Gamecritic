<?php
require_once __DIR__ . '/BaseModel.php';

class AdminModel extends BaseModel {
    protected $table = 'admins';

    public function getAdminEmails(): array {
        $emails = [];
        try {
            $stmt = $this->db->prepare("SELECT email FROM {$this->table}");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $emails[] = $row['email'];
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error fetching admin emails: " . $e->getMessage());
            $emails = ['admin@gamecritic.com'];
        }
        return $emails;
    }

    public function getAdminByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function getAllAdmins(): array {
        return $this->findAll();
    }
}

