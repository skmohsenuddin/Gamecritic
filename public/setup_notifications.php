<?php
/**
 * Setup script for Notifications & Followers tables
 * Access this file via browser: http://localhost/Gamecritic/public/setup_notifications.php
 */

require_once __DIR__ . '/../app/config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("<h2>Error: Could not connect to database.</h2><p>Please check your database configuration.</p>");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Notifications - GameCritic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background: #f5f5f5;
        }
        .setup-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h2>🔔 Notifications & Followers Setup</h2>
        <p class="text-muted">This script will create the necessary database tables for the notifications feature.</p>
        <hr>
        
        <?php
        $success = true;
        $messages = [];
        
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

        try {
            // Create followers table
            echo "<div class='mb-3'>";
            echo "<strong>Creating followers table...</strong><br>";
            if ($db->query($sql_followers)) {
                echo "<span class='success'>✓ Followers table created successfully!</span>";
                $messages[] = "Followers table: OK";
            } else {
                echo "<span class='error'>✗ Error creating followers table: " . htmlspecialchars($db->error) . "</span>";
                $messages[] = "Followers table: ERROR - " . $db->error;
                $success = false;
            }
            echo "</div>";
            
            // Create notifications table
            echo "<div class='mb-3'>";
            echo "<strong>Creating notifications table...</strong><br>";
            if ($db->query($sql_notifications)) {
                echo "<span class='success'>✓ Notifications table created successfully!</span>";
                $messages[] = "Notifications table: OK";
            } else {
                echo "<span class='error'>✗ Error creating notifications table: " . htmlspecialchars($db->error) . "</span>";
                $messages[] = "Notifications table: ERROR - " . $db->error;
                $success = false;
            }
            echo "</div>";
            
            if ($success) {
                echo "<div class='alert alert-success mt-4'>";
                echo "<h4>✓ Setup Completed Successfully!</h4>";
                echo "<p>You can now use the Notifications & Digest feature.</p>";
                echo "<a href='/' class='btn btn-primary'>Go to Homepage</a>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-danger mt-4'>";
                echo "<h4>✗ Setup Completed with Errors</h4>";
                echo "<p>Please check the error messages above and try again.</p>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger mt-4'>";
            echo "<h4>✗ Error</h4>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>Please check your database connection and try again.</p>";
            echo "</div>";
            $success = false;
        }
        ?>
        
        <hr class="mt-4">
        <div class="text-muted small">
            <strong>Note:</strong> You can safely run this script multiple times. It uses "CREATE TABLE IF NOT EXISTS" 
            so it won't cause errors if the tables already exist.
        </div>
    </div>
</body>
</html>
<?php
$db->close();
?>

