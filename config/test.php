<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Verification Script</h1>";

function displayStatus($message, $success) {
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid " . 
        ($success ? "#4CAF50" : "#F44336") . "; border-radius: 5px;'>";
    echo "<strong style='color: " . ($success ? "#4CAF50" : "#F44336") . ";'>" . 
        ($success ? "✓ PASS" : "✗ FAIL") . ":</strong> $message";
    echo "</div>";
}

// Try connecting as root to verify MySQL is running
try {
    $rootConn = new PDO("mysql:host=localhost", "root", "");
    displayStatus("MySQL Server is running", true);
    
    // Check if database exists
    $stmt = $rootConn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'tranablog'");
    $dbExists = $stmt->fetch();
    displayStatus("Database 'tranablog' exists", $dbExists !== false);
    
    // Check if user exists
    $stmt = $rootConn->query("SELECT User FROM mysql.user WHERE User = 'bloguser'");
    $userExists = $stmt->fetch();
    displayStatus("User 'bloguser' exists", $userExists !== false);
    
    if (!$dbExists) {
        echo "<h2>Database not found. Please run this SQL:</h2>";
        echo "<pre>
CREATE DATABASE tranablog;
USE tranablog;

-- Create user with necessary permissions
CREATE USER IF NOT EXISTS 'bloguser'@'localhost' IDENTIFIED BY 'p@ssword';
GRANT ALL PRIVILEGES ON tranablog.* TO 'bloguser'@'localhost';
FLUSH PRIVILEGES;
</pre>";
        echo "<p>Then run schema.sql to create the tables.</p>";
    }
    
    if ($dbExists) {
        // Check if tables exist
        $conn = new PDO("mysql:host=localhost;dbname=tranablog", "root", "");
        $tables = ['roles', 'users', 'users_info', 'topics', 'posts', 'comments', 'rankings'];
        
        foreach ($tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            $tableExists = $stmt->fetch();
            displayStatus("Table '$table' exists", $tableExists !== false);
        }
    }
    
} catch (PDOException $e) {
    displayStatus("MySQL Connection Error: " . $e->getMessage(), false);
    if (strpos($e->getMessage(), "Access denied") !== false) {
        echo "<p>Please provide the root password when running the SQL commands.</p>";
    }
}
?>
