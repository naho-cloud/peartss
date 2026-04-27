<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set test session if needed
if (!isset($_SESSION['user_id']) && isset($_GET['test'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_role'] = 'student';
    $_SESSION['user_email'] = 'test@example.com';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lost Module Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Lost Module Test Page</h2>
        
        <div class="card mb-4">
            <div class="card-header">Session Status</div>
            <div class="card-body">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p class="text-success">✓ Logged in as: <?php echo $_SESSION['user_name']; ?> (<?php echo $_SESSION['user_role']; ?>)</p>
                <?php else: ?>
                    <p class="text-danger">✗ Not logged in</p>
                    <a href="?test=1" class="btn btn-primary">Set Test Session</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Test Links</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="views/lost/report.php">Lost Report Page</a>
                                <?php echo file_exists('views/lost/report.php') ? '✅' : '❌'; ?>
                            </li>
                            <li class="list-group-item">
                                <a href="views/lost/list.php">Lost List Page</a>
                                <?php echo file_exists('views/lost/list.php') ? '✅' : '❌'; ?>
                            </li>
                            <li class="list-group-item">
                                <a href="views/found/report.php">Found Report Page</a>
                                <?php echo file_exists('views/found/report.php') ? '✅' : '❌'; ?>
                            </li>
                            <li class="list-group-item">
                                <a href="views/found/list.php">Found List Page</a>
                                <?php echo file_exists('views/found/list.php') ? '✅' : '❌'; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Debug Info</div>
                    <div class="card-body">
                        <p>PHP Version: <?php echo phpversion(); ?></p>
                        <p>Session ID: <?php echo session_id(); ?></p>
                        <p>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
                        <p>Script Path: <?php echo __FILE__; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>