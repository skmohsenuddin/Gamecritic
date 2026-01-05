<?php
/**
 * Quick Setup - Run this file directly to create tables
 * Usage: php quick_setup_notifications.php
 * Or access via browser: http://localhost/Gamecritic/quick_setup_notifications.php
 */

require_once __DIR__ . '/app/config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Error: Could not connect to database.\n");
}

echo "Creating notifications and followers tables...\n\n";

// Create followers table
$sql_followers = "
CREATE TABLE IF NOT EXISTS followers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_follow (follower_id, following_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

// Create notifications table
$sql_notifications = "
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('new_review', 'followed_reviewer_review', 'new_game') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(500) DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

$success = true;

// Create followers table
echo "Creating followers table... ";
if ($db->query($sql_followers)) {
    echo "✓ Success!\n";
} else {
    echo "✗ Error: " . $db->error . "\n";
    $success = false;
}

// Create notifications table
echo "Creating notifications table... ";
if ($db->query($sql_notifications)) {
    echo "✓ Success!\n";
} else {
    echo "✗ Error: " . $db->error . "\n";
    $success = false;
}

if ($success) {
    echo "\n✓ Setup completed successfully!\n";
    echo "You can now use the Notifications & Digest feature.\n";
} else {
    echo "\n✗ Setup completed with errors.\n";
    echo "Please check the error messages above.\n";
}

$db->close();
?>

