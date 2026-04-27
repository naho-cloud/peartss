<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Lost Module Debugger</h2>";

// Check if user is logged in
echo "<h3>Session Status:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p style='color:green'>✓ User is logged in: " . $_SESSION['user_name'] . " (Role: " . $_SESSION['user_role'] . ")</p>";
} else {
    echo "<p style='color:red'>✗ User is NOT logged in</p>";
}

// Check required files
echo "<h3>Required Files:</h3>";
$files = [
    '/config/constants.php',
    '/config/functions.php',
    '/config/database.php',
    '/models/LostReport.php',
    '/models/GeneralLostItem.php',
    '/controllers/LostController.php',
    '/views/lost/report.php'
];

foreach ($files as $file) {
    $full_path = __DIR__ . $file;
    if (file_exists($full_path)) {
        echo "<p style='color:green'>✓ Found: $file</p>";
    } else {
        echo "<p style='color:red'>✗ NOT Found: $file</p>";
    }
}

// Try to include files
echo "<h3>Testing Includes:</h3>";
try {
    echo "<p>Including constants.php...</p>";
    require_once __DIR__ . '/config/constants.php';
    echo "<p style='color:green'>✓ Constants loaded</p>";
    
    echo "<p>Including functions.php...</p>";
    require_once __DIR__ . '/config/functions.php';
    echo "<p style='color:green'>✓ Functions loaded</p>";
    
    echo "<p>Including database.php...</p>";
    require_once __DIR__ . '/config/database.php';
    echo "<p style='color:green'>✓ Database loaded</p>";
    
    echo "<p>Including LostReport model...</p>";
    require_once __DIR__ . '/models/LostReport.php';
    echo "<p style='color:green'>✓ LostReport model loaded</p>";
    
    echo "<p>Including GeneralLostItem model...</p>";
    require_once __DIR__ . '/models/GeneralLostItem.php';
    echo "<p style='color:green'>✓ GeneralLostItem model loaded</p>";
    
    echo "<p>Including LostController...</p>";
    require_once __DIR__ . '/controllers/LostController.php';
    echo "<p style='color:green'>✓ LostController loaded</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test database connection
echo "<h3>Database Connection:</h3>";
try {
    $db = getDB();
    if ($db) {
        echo "<p style='color:green'>✓ Database connected</p>";
        
        // Check if tables exist
        $tables = ['lost_reports', 'general_lost_items', 'found_reports', 'general_found_items'];
        foreach ($tables as $table) {
            $result = $db->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                echo "<p style='color:green'>✓ Table '$table' exists</p>";
            } else {
                echo "<p style='color:red'>✗ Table '$table' does NOT exist</p>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Database error: " . $e->getMessage() . "</p>";
}

// Test LostController
echo "<h3>Testing LostController:</h3>";
try {
    $controller = new LostController();
    echo "<p style='color:green'>✓ LostController instantiated</p>";
    
    // Test method existence
    if (method_exists($controller, 'showReportPage')) {
        echo "<p style='color:green'>✓ showReportPage method exists</p>";
    } else {
        echo "<p style='color:red'>✗ showReportPage method missing</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error creating LostController: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color:red'>Error creating LostController: " . $e->getMessage() . "</p>";
}

echo "<h3>Debug Complete</h3>";
?>