<?php
require_once __DIR__ . '/../app/config/database.php';

$message = '';
$success = false;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS gameplays (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        video_path VARCHAR(500) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_game_id (game_id),
        INDEX idx_created_at (created_at)
    )";
    
    if ($conn->query($sql)) {
        $message = "✅ Gameplays table created successfully!";
        $success = true;
    } else {
        $message = "❌ Error creating table: " . $conn->error;
    }
    
    $uploadDir = __DIR__ . '/uploads/gameplays/';
    if (!is_dir($uploadDir)) {
        if (mkdir($uploadDir, 0755, true)) {
            $message .= "<br>✅ Upload directory created: uploads/gameplays/";
        } else {
            $message .= "<br>❌ Failed to create upload directory";
        }
    } else {
        $message .= "<br>✅ Upload directory already exists";
    }
    
} catch (Exception $e) {
    $message = "❌ Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Gameplays</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f0f0f;
            color: #ffffff;
            padding: 50px;
        }
        .container {
            max-width: 600px;
        }
        .alert {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card bg-dark text-white">
            <div class="card-header">
                <h2>🎮 Gameplays Setup</h2>
            </div>
            <div class="card-body">
                <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo $message; ?>
                </div>
                <?php if ($success): ?>
                    <a href="/Gamecritic/public/" class="btn btn-primary">Go to Home</a>
                    <a href="/Gamecritic/public/gameplays" class="btn btn-success">Go to Gameplays</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

