<?php
// Enable maximum error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to catch any errors
ob_start();

echo "<h1>Admin Users List - Debug Mode</h1>";

try {
    // Step 1: Test basic PHP
    echo "<p>Step 1: Basic PHP is working ✓</p>";
    
    // Step 2: Check if we can connect to database
    echo "<p>Step 2: Attempting database connection...</p>";
    
    $host = 'localhost';
    $dbname = 'pearts_db';
    $username = 'root';
    $password = '';
    
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green'>✓ Database connected successfully</p>";
    
    // Step 3: Check if users table exists
    echo "<p>Step 3: Checking if users table exists...</p>";
    
    $tables = $db->query("SHOW TABLES LIKE 'users'");
    if ($tables->rowCount() > 0) {
        echo "<p style='color:green'>✓ Users table exists</p>";
        
        // Step 4: Count total users
        $count = $db->query("SELECT COUNT(*) as total FROM users");
        $total = $count->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total users in database: " . $total['total'] . "</p>";
        
        // Step 5: Get all admin users
        echo "<p>Step 5: Fetching admin users...</p>";
        
        $query = "SELECT user_id, university_id, full_name, email, role, status FROM users WHERE role = 'admin'";
        $result = $db->query($query);
        $admins = $result->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($admins) > 0) {
            echo "<h2>Found " . count($admins) . " Admin User(s):</h2>";
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
            echo "<tr style='background-color: #f2f2f2;'><th>ID</th><th>University ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
            
            foreach ($admins as $admin) {
                $rowColor = ($admin['status'] == 'active') ? 'style="background-color: #d4edda;"' : '';
                echo "<tr $rowColor>";
                echo "<td>" . $admin['user_id'] . "</td>";
                echo "<td><strong>" . htmlspecialchars($admin['university_id']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($admin['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
                echo "<td>" . htmlspecialchars($admin['role']) . "</td>";
                echo "<td>" . htmlspecialchars($admin['status']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:orange'>No admin users found in the database.</p>";
            
            // Show all users to see what roles exist
            echo "<h3>All Users (to see available roles):</h3>";
            $all = $db->query("SELECT user_id, university_id, full_name, role, status FROM users LIMIT 10");
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>University ID</th><th>Name</th><th>Role</th><th>Status</th></tr>";
            while ($user = $all->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $user['user_id'] . "</td>";
                echo "<td>" . htmlspecialchars($user['university_id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "<td>" . htmlspecialchars($user['status']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color:red'>✗ Users table does not exist!</p>";
        echo "<p>Please run the database schema first.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Error Code: " . $e->getCode() . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>General Error: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color:red'>PHP Error: " . $e->getMessage() . "</p>";
}

// Show any errors that might have been caught
$errors = ob_get_clean();
echo $errors;

echo "<h3>Quick Actions:</h3>";
echo "<ul>";
echo "<li><a href='make_nahom_admin.php'>Make Nahom Admin</a></li>";
echo "<li><a href='check_my_session.php'>Check My Session</a></li>";
echo "<li><a href='views/admin/dashboard.php'>Try Admin Dashboard</a></li>";
echo "</ul>";
?>