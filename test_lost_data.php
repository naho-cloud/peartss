<?php
// test_lost_data.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/functions.php';
require_once __DIR__ . '/models/GeneralLostItem.php';
require_once __DIR__ . '/models/LostReport.php';

session_start();

echo "<h1>Lost Items Data Test</h1>";

try {
    $db = getDB();
    
    // Check if tables exist
    echo "<h2>1. Checking Tables:</h2>";
    $tables = ['lost_items', 'lost_reports'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        echo "$table: " . ($result->rowCount() > 0 ? "✓ Exists" : "✗ Missing") . "<br>";
    }
    
    // Check lost_items table data
    echo "<h2>2. Lost Items Table Data:</h2>";
    $items = $db->query("SELECT * FROM lost_items ORDER BY reported_date DESC");
    echo "Total records: " . $items->rowCount() . "<br>";
    
    if ($items->rowCount() > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Item Type</th><th>Description</th><th>Status</th><th>Image Path</th></tr>";
        foreach ($items as $item) {
            echo "<tr>";
            echo "<td>" . $item['id'] . "</td>";
            echo "<td>" . $item['user_id'] . "</td>";
            echo "<td>" . $item['item_type'] . "</td>";
            echo "<td>" . $item['item_description'] . "</td>";
            echo "<td>" . $item['status'] . "</td>";
            echo "<td>" . ($item['image_path'] ?: 'No image') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found in lost_items table.</p>";
    }
    
    // Test GeneralLostItem model
    echo "<h2>3. Testing GeneralLostItem Model:</h2>";
    $generalModel = new GeneralLostItem($db);
    $allItems = $generalModel->getAllLostItems();
    echo "getAllLostItems() returned: " . count($allItems) . " items<br>";
    
    if (isset($_SESSION['user_id'])) {
        $userItems = $generalModel->getUserLostItems($_SESSION['user_id']);
        echo "getUserLostItems() for user {$_SESSION['user_id']}: " . count($userItems) . " items<br>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>