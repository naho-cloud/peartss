<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Dashboard Setup</h1>";

// Test database connection
try {
    $db = new PDO("mysql:host=localhost;dbname=pearts_db", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✓ Database connected</p>";
    
    // Check if users table exists
    $result = $db->query("SHOW TABLES LIKE 'users'");
    if ($result->rowCount() > 0) {
        echo "<p style='color:green'>✓ Users table exists</p>";
        
        // Check for admin user
        $stmt = $db->query("SELECT * FROM users WHERE role = 'admin'");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($admins) > 0) {
            echo "<p style='color:green'>✓ Admin users found:</p>";
            foreach ($admins as $admin) {
                echo "<p> - " . $admin['full_name'] . " (" . $admin['university_id'] . ")</p>";
            }
        } else {
            echo "<p style='color:red'>✗ No admin users found. Creating one...</p>";
            
            // Create admin user
            $password = password_hash('Admin@123', PASSWORD_DEFAULT);
            $insert = $db->prepare("INSERT INTO users (university_id, full_name, email, password, role, status) VALUES (?, ?, ?, ?, 'admin', 'active')");
            $insert->execute(['ADMIN001', 'System Administrator', 'admin@pearts.edu.et', $password]);
            echo "<p style='color:green'>✓ Admin user created: ADMIN001 / Admin@123</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Users table does not exist. Please run the database schema first.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Database Error: " . $e->getMessage() . "</p>";
}
?>

<p><a href="views/auth/login.php">Go to Login Page</a></p>