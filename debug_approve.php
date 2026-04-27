<?php
// debug_approve.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/functions.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Debugging Approve Function</h1>";

try {
    $db = getDB();
    
    // Check table structure
    echo "<h2>1. Table Structure:</h2>";
    $columns = $db->query("DESCRIBE asset_registration_requests");
    
    if ($columns->rowCount() == 0) {
        echo "<p style='color:red'>Table 'asset_registration_requests' does not exist!</p>";
        echo "<h3>Creating table now...</h3>";
        
        $create_sql = "CREATE TABLE IF NOT EXISTS `asset_registration_requests` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `reason` text NOT NULL,
            `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            `admin_notes` text DEFAULT NULL,
            `requested_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `processed_date` timestamp NULL DEFAULT NULL,
            `processed_by` int(11) DEFAULT NULL,
            `current_assets` int(11) DEFAULT 0,
            `requested_assets` int(11) DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($create_sql);
        echo "<p style='color:green'>Table created successfully!</p>";
        
        // Refresh columns info
        $columns = $db->query("DESCRIBE asset_registration_requests");
    }
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show all pending requests
    echo "<h2>2. Pending Requests:</h2>";
    $pending = $db->query("SELECT * FROM asset_registration_requests WHERE status = 'pending'");
    
    if ($pending->rowCount() > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo " hilab<th>ID</th><th>User ID</th><th>Reason</th><th>Status</th><th>Request Date</th><th>Action</th></tr>";
        foreach ($pending as $row) {
            $request_id = $row['id'] ?? 'N/A';
            echo "<tr>";
            echo "<td>" . $request_id . "</td>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . substr($row['reason'], 0, 50) . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['requested_date'] . "</td>";
            echo "<td>";
            echo "<button onclick='testApprove(" . $request_id . ")'>Test Approve</button> ";
            echo "<button onclick='testReject(" . $request_id . ")'>Test Reject</button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No pending requests found.</p>";
        
        // Create a test request
        echo "<h3>Creating a test request...</h3>";
        
        // Get a valid user ID
        $user_check = $db->query("SELECT user_id FROM users LIMIT 1");
        $user = $user_check->fetch();
        
        if ($user) {
            $test_user_id = $user['user_id'];
            $insert = $db->prepare("INSERT INTO asset_registration_requests (user_id, reason, status, requested_date) 
                                    VALUES (?, 'Test request for debugging', 'pending', NOW())");
            $insert->execute([$test_user_id]);
            echo "<p style='color:green'>Test request created with ID: " . $db->lastInsertId() . "</p>";
            echo "<meta http-equiv='refresh' content='2'>";
        } else {
            echo "<p style='color:red'>No users found in database. Please create a user first.</p>";
        }
    }
    
    // Handle test approve
    if (isset($_GET['approve_id'])) {
        $id = intval($_GET['approve_id']);
        echo "<h2>3. Testing Approve for ID: $id</h2>";
        
        // Method 1: Update by 'id' column
        echo "<h3>Method 1: Update by 'id' column</h3>";
        $sql1 = "UPDATE asset_registration_requests SET status = 'approved', processed_date = NOW() WHERE id = ?";
        $stmt1 = $db->prepare($sql1);
        $result1 = $stmt1->execute([$id]);
        echo "Result: " . ($result1 ? "Success" : "Failed") . "<br>";
        echo "Rows affected: " . $stmt1->rowCount() . "<br>";
        
        if ($stmt1->rowCount() > 0) {
            echo "<p style='color:green'>✓ APPROVE SUCCESSFUL! Method 1 works.</p>";
        } else {
            // Method 2: Update by 'request_id' column
            echo "<h3>Method 2: Update by 'request_id' column</h3>";
            $sql2 = "UPDATE asset_registration_requests SET status = 'approved', processed_date = NOW() WHERE request_id = ?";
            $stmt2 = $db->prepare($sql2);
            $result2 = $stmt2->execute([$id]);
            echo "Result: " . ($result2 ? "Success" : "Failed") . "<br>";
            echo "Rows affected: " . $stmt2->rowCount() . "<br>";
            
            if ($stmt2->rowCount() > 0) {
                echo "<p style='color:green'>✓ APPROVE SUCCESSFUL! Method 2 works.</p>";
            }
        }
        
        echo "<p><a href='debug_approve.php'>Back to list</a></p>";
    }
    
    // Handle test reject
    if (isset($_GET['reject_id'])) {
        $id = intval($_GET['reject_id']);
        echo "<h2>3. Testing Reject for ID: $id</h2>";
        
        $sql = "UPDATE asset_registration_requests SET status = 'rejected', processed_date = NOW() WHERE id = ?";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([$id]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo "<p style='color:green'>✓ REJECT SUCCESSFUL!</p>";
        } else {
            echo "<p style='color:red'>✗ REJECT FAILED! Trying with request_id...</p>";
            
            $sql2 = "UPDATE asset_registration_requests SET status = 'rejected', processed_date = NOW() WHERE request_id = ?";
            $stmt2 = $db->prepare($sql2);
            $result2 = $stmt2->execute([$id]);
            
            if ($result2 && $stmt2->rowCount() > 0) {
                echo "<p style='color:green'>✓ REJECT SUCCESSFUL using request_id!</p>";
            } else {
                echo "<p style='color:red'>✗ REJECT FAILED!</p>";
            }
        }
        
        echo "<p><a href='debug_approve.php'>Back to list</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>

<script>
function testApprove(id) {
    if (confirm('Test approve request ID: ' + id + '?')) {
        window.location.href = 'debug_approve.php?approve_id=' + id;
    }
}
function testReject(id) {
    if (confirm('Test reject request ID: ' + id + '?')) {
        window.location.href = 'debug_approve.php?reject_id=' + id;
    }
}
</script>