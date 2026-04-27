<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Update Script</h1>";

try {
    $db = new PDO("mysql:host=localhost;dbname=pearts_db", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green'>✓ Connected to database</p>";
    
    // Check if found_reports table exists
    $result = $db->query("SHOW TABLES LIKE 'found_reports'");
    if ($result->rowCount() > 0) {
        echo "<p style='color:green'>✓ found_reports table exists</p>";
        
        // Check if storage_location column exists
        $columns = $db->query("SHOW COLUMNS FROM found_reports LIKE 'storage_location'");
        if ($columns->rowCount() == 0) {
            // Add storage_location column
            $db->exec("ALTER TABLE found_reports ADD COLUMN storage_location VARCHAR(255) DEFAULT 'Security Office' AFTER found_date");
            echo "<p style='color:green'>✓ Added storage_location column</p>";
        } else {
            echo "<p style='color:blue'>✓ storage_location column already exists</p>";
        }
        
        // Check if found_date column exists
        $date_column = $db->query("SHOW COLUMNS FROM found_reports LIKE 'found_date'");
        if ($date_column->rowCount() == 0) {
            // Add found_date column
            $db->exec("ALTER TABLE found_reports ADD COLUMN found_date DATE AFTER found_location");
            echo "<p style='color:green'>✓ Added found_date column</p>";
        } else {
            echo "<p style='color:blue'>✓ found_date column already exists</p>";
        }
        
    } else {
        echo "<p style='color:red'>✗ found_reports table does not exist!</p>";
    }
    
    // Check other tables
    $tables = ['lost_reports', 'assets', 'users', 'campuses'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "<p style='color:green'>✓ $table table exists</p>";
        } else {
            echo "<p style='color:red'>✗ $table table does not exist!</p>";
        }
    }
    
    echo "<h3>Update Complete!</h3>";
    echo "<p><a href='views/security/dashboard.php'>Go to Security Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>