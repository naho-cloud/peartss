<?php
// debug_approve_check.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Debug: Approve and Max Assets Check</h1>";

try {
    $db = getDB();
    
    // Get a user to test with
    $test_user_id = 2; // Change this to a real user ID
    
    echo "<h2>1. Current User Status</h2>";
    $user_check = $db->prepare("SELECT user_id, full_name, max_assets FROM users WHERE user_id = ?");
    $user_check->execute([$test_user_id]);
    $user = $user_check->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p>User: {$user['full_name']} (ID: {$user['user_id']})</p>";
        echo "<p>Current max_assets: <strong>" . $user['max_assets'] . "</strong></p>";
        
        // Count current assets
        $asset_count = $db->prepare("SELECT COUNT(*) FROM assets WHERE user_id = ? AND status != 'deactivated'");
        $asset_count->execute([$test_user_id]);
        $current_assets = $asset_count->fetchColumn();
        echo "<p>Current assets owned: <strong>" . $current_assets . "</strong></p>";
        echo "<p>Remaining slots: <strong>" . ($user['max_assets'] - $current_assets) . "</strong></p>";
    }
    
    echo "<h2>2. Pending Requests for User</h2>";
    $requests = $db->prepare("SELECT * FROM asset_registration_requests WHERE user_id = ? AND status = 'pending'");
    $requests->execute([$test_user_id]);
    
    if ($requests->rowCount() > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Reason</th><th>Status</th><th>Requested Date</th><th>Action</th></tr>";
        foreach ($requests as $req) {
            echo "<tr>";
            echo "<td>" . $req['id'] . "</td>";
            echo "<td>" . $req['reason'] . "</td>";
            echo "<td>" . $req['status'] . "</td>";
            echo "<td>" . $req['requested_date'] . "</td>";
            echo "<td>";
            echo "<button onclick='approveRequest(" . $req['id'] . ", " . $test_user_id . ")'>Approve This Request</button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No pending requests for this user.</p>";
        
        // Create a test request
        echo "<h3>Creating a test request...</h3>";
        $insert = $db->prepare("INSERT INTO asset_registration_requests (user_id, reason, status, requested_date) 
                                VALUES (?, 'Test request for max_assets fix', 'pending', NOW())");
        $insert->execute([$test_user_id]);
        echo "<p>Test request created. <a href='debug_approve_check.php'>Refresh page</a></p>";
    }
    
    // Handle approve action
    if (isset($_GET['approve_id']) && isset($_GET['user_id'])) {
        $request_id = $_GET['approve_id'];
        $user_id = $_GET['user_id'];
        
        echo "<h2>3. Approving Request ID: $request_id</h2>";
        
        // Start transaction
        $db->beginTransaction();
        
        try {
            // Get the request
            $get_req = $db->prepare("SELECT * FROM asset_registration_requests WHERE id = ?");
            $get_req->execute([$request_id]);
            $request = $get_req->fetch(PDO::FETCH_ASSOC);
            
            echo "<p>Request found: " . print_r($request, true) . "</p>";
            
            // Update user's max_assets
            $update_user = $db->prepare("UPDATE users SET max_assets = max_assets + 1 WHERE user_id = ?");
            $update_user->execute([$user_id]);
            $user_updated = $update_user->rowCount();
            
            echo "<p>User max_assets updated: " . ($user_updated ? "Yes" : "No") . "</p>";
            
            // Update request status
            $update_req = $db->prepare("UPDATE asset_registration_requests SET status = 'approved', processed_date = NOW() WHERE id = ?");
            $update_req->execute([$request_id]);
            $req_updated = $update_req->rowCount();
            
            echo "<p>Request status updated: " . ($req_updated ? "Yes" : "No") . "</p>";
            
            if ($user_updated > 0 && $req_updated > 0) {
                $db->commit();
                echo "<p style='color:green'>✓ APPROVE SUCCESSFUL! User can now register more assets.</p>";
            } else {
                $db->rollBack();
                echo "<p style='color:red'>✗ APPROVE FAILED! Changes rolled back.</p>";
            }
            
        } catch (Exception $e) {
            $db->rollBack();
            echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
        }
        
        echo "<p><a href='debug_approve_check.php'>Check updated values</a></p>";
    }
    
    // Show updated user info after approval
    if ($user) {
        $updated_user = $db->prepare("SELECT max_assets FROM users WHERE user_id = ?");
        $updated_user->execute([$test_user_id]);
        $new_max = $updated_user->fetchColumn();
        
        echo "<h2>4. Updated User Status</h2>";
        echo "<p>New max_assets: <strong style='color:green'>" . $new_max . "</strong></p>";
        echo "<p>Current assets: <strong>" . $current_assets . "</strong></p>";
        echo "<p>Remaining slots: <strong>" . ($new_max - $current_assets) . "</strong></p>";
        
        if (($new_max - $current_assets) > 0) {
            echo "<p style='color:green; font-weight:bold;'>✓ User can now register " . ($new_max - $current_assets) . " more asset(s)!</p>";
        } else {
            echo "<p style='color:red;'>✗ User still cannot register more assets. Max assets not increased properly.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

?>

<script>
function approveRequest(requestId, userId) {
    if (confirm('Approve this request?')) {
        window.location.href = 'debug_approve_check.php?approve_id=' + requestId + '&user_id=' + userId;
    }
}
</script>