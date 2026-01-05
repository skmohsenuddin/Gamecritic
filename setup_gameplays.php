<?php
require_once __DIR__ . '/app/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "🔍 Setting up gameplays table...\n\n";
    
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
        echo "✅ Gameplays table created successfully!\n";
    } else {
        echo "❌ Error creating table: " . $conn->error . "\n";
    }
    
    $uploadDir = __DIR__ . '/public/uploads/gameplays/';
    if (!is_dir($uploadDir)) {
        if (mkdir($uploadDir, 0755, true)) {
            echo "✅ Upload directory created: {$uploadDir}\n";
        } else {
            echo "❌ Failed to create upload directory: {$uploadDir}\n";
        }
    } else {
        echo "✅ Upload directory already exists: {$uploadDir}\n";
    }
    
    echo "\n🎮 Gameplays feature setup complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

