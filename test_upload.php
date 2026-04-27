<?php
// check_images.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Checking Lost Item Images</h1>";

$upload_dir = __DIR__ . '/uploads/lost_items/';
echo "<h2>Upload Directory: $upload_dir</h2>";

if (file_exists($upload_dir)) {
    echo "<p style='color:green'>✓ Upload directory exists</p>";
    
    $files = scandir($upload_dir);
    echo "<h3>Files in directory:</h3>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $file_path = $upload_dir . $file;
            $file_size = filesize($file_path);
            echo "<li>$file - " . round($file_size / 1024, 2) . " KB</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>✗ Upload directory does NOT exist!</p>";
    echo "<p>Creating directory...</p>";
    mkdir($upload_dir, 0777, true);
    echo "<p style='color:green'>Directory created</p>";
}

// Check database records
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

$db = getDB();
$query = "SELECT id, item_type, image_path FROM lost_items WHERE image_path IS NOT NULL AND image_path != ''";
$stmt = $db->prepare($query);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Database Records with Images:</h2>";
if (count($items) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Item Type</th><th>Image Path in DB</th><th>File Exists?</th><th>Full URL</th></tr>";
    foreach ($items as $item) {
        $file_exists = file_exists(__DIR__ . '/' . $item['image_path']);
        $full_url = SITE_URL . '/' . ltrim($item['image_path'], '/');
        echo "<tr>";
        echo "<td>{$item['id']}</td>";
        echo "<td>{$item['item_type']}</td>";
        echo "<td>{$item['image_path']}</td>";
        echo "<td>" . ($file_exists ? "✓ Yes" : "✗ No") . "</td>";
        echo "<td><a href='$full_url' target='_blank'>$full_url</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No images found in database.</p>";
}
?>