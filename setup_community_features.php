<?php
/**
 * Setup script for community features (Polls, Suggestions, Bug Reports)
 * Run this once to create the necessary database tables
 */

require_once __DIR__ . '/app/config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Error: Could not connect to database.\n");
}

echo "Setting up community features tables...\n\n";

// Read and execute the SQL file
$sqlFile = __DIR__ . '/community_features.sql';
if (!file_exists($sqlFile)) {
    die("Error: community_features.sql not found!\n");
}

$sql = file_get_contents($sqlFile);

// Split by semicolon to execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue; // Skip comments and empty statements
    }
    
    if (strpos($statement, 'USE ') === 0) {
        continue; // Skip USE statements
    }
    
    if ($db->query($statement)) {
        echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
    } else {
        echo "✗ Error: " . $db->error . "\n";
        echo "  Statement: " . substr($statement, 0, 100) . "...\n";
    }
}

echo "\n✓ Community features setup complete!\n";
echo "You can now use:\n";
echo "  - /polls - View and vote on polls\n";
echo "  - /suggestion - Get AI game suggestions\n";
echo "  - /report_bug - Report bugs\n";
echo "  - /bugs - View all bug reports (admin only)\n";
?>


