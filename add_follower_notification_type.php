<?php
/**
 * Add 'new_follower' type to notifications table
 * Run this file to update the notifications table
 */

require_once __DIR__ . '/app/config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Error: Could not connect to database.\n");
}

echo "Updating notifications table to add 'new_follower' type...\n\n";

// First, check if we need to alter the ENUM
$checkStmt = $db->prepare("SHOW COLUMNS FROM notifications WHERE Field = 'type'");
$checkStmt->execute();
$result = $checkStmt->get_result();
$column = $result->fetch_assoc();

if ($column) {
    $currentType = $column['Type'];
    if (strpos($currentType, 'new_follower') === false) {
        // Alter the ENUM to include new_follower
        $sql = "ALTER TABLE notifications MODIFY COLUMN type ENUM('new_review', 'followed_reviewer_review', 'new_game', 'new_follower') NOT NULL";
        
        if ($db->query($sql)) {
            echo "✓ Successfully added 'new_follower' type to notifications table!\n";
        } else {
            echo "✗ Error: " . $db->error . "\n";
        }
    } else {
        echo "✓ 'new_follower' type already exists in notifications table.\n";
    }
} else {
    echo "✗ Error: Could not find 'type' column in notifications table.\n";
}

$db->close();
?>

