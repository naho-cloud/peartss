<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set test session if needed
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_role'] = 'student';
    $_SESSION['user_email'] = 'test@example.com';
}

// Simple function to check login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$page_title = 'Report Lost Item - Simple Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4>Report Lost Item (Simple Test)</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <strong>Success!</strong> The page is working!
                        </div>
                        
                        <p>This is a simplified version to test if the page loads.</p>
                        
                        <div class="mt-4">
                            <h5>Session Info:</h5>
                            <ul>
                                <li>User ID: <?php echo $_SESSION['user_id'] ?? 'Not set'; ?></li>
                                <li>User Name: <?php echo $_SESSION['user_name'] ?? 'Not set'; ?></li>
                                <li>User Role: <?php echo $_SESSION['user_role'] ?? 'Not set'; ?></li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="views/lost/report.php" class="btn btn-primary">Go to Actual Lost Report Page</a>
                            <a href="debug_lost.php" class="btn btn-info">Run Debugger</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>