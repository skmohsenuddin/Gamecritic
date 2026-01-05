<?php
/**
 * Quick Setup - Run this file directly to create chat messages table
 * Usage: php quick_setup_chat.php
 * Or access via browser: http://localhost/Gamecritic/quick_setup_chat.php
 */

require_once __DIR__ . '/app/config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Error: Could not connect to database.\n");
}

echo "Creating messages table...\n\n";

// Create messages table
$sql_messages = "
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_conversation (sender_id, receiver_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

$success = true;

// Create messages table
echo "Creating messages table... ";
if ($db->query($sql_messages)) {
    echo "✓ Success!\n";
} else {
    echo "✗ Error: " . $db->error . "\n";
    $success = false;
}

if ($success) {
    echo "\n✓ Chat setup completed successfully!\n";
    echo "You can now use the Chat feature.\n";
} else {
    echo "\n✗ Setup completed with errors.\n";
    echo "Please check the error messages above.\n";
}

$db->close();
?>

