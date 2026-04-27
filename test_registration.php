<?php
// test_registration.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing RegistrationRequest Class</h1>";

// Try to include the file
require_once __DIR__ . '/models/RegistrationRequest.php';

if (class_exists('RegistrationRequest')) {
    echo "<p style='color:green'>✓ RegistrationRequest class exists</p>";
    
    // Try to create an instance
    try {
        $db = getDB();
        $request_model = new RegistrationRequest($db);
        echo "<p style='color:green'>✓ RegistrationRequest instance created</p>";
        
        // Test getPendingRequests
        $pending = $request_model->getPendingRequests();
        echo "<p>Pending requests count: " . count($pending) . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ RegistrationRequest class does not exist</p>";
}

// Check if the file was included multiple times
$included_files = get_included_files();
echo "<h2>Included Files:</h2>";
echo "<ul>";
foreach ($included_files as $file) {
    if (strpos($file, 'RegistrationRequest') !== false) {
        echo "<li style='color:orange'>" . basename($file) . "</li>";
    }
}
echo "</ul>";
?>