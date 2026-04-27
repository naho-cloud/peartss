<?php
// Public page to view all lost items
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    $db = null;
}

// Get all public lost items
$public_lost_items = [];
$public_general_lost = [];

if ($db) {
    try {
        $lost_query = "SELECT l.*, a.brand, a.model, a.serial_number, a.asset_image,
                       u.full_name as owner_name
                       FROM lost_reports l
                       JOIN assets a ON l.asset_id = a.asset_id
                       JOIN users u ON a.user_id = u.user_id
                       WHERE l.status IN ('pending', 'investigating') 
                       AND l.public_visibility = 1
                       ORDER BY l.reported_date DESC
                       LIMIT 50";
        $lost_stmt = $db->prepare($lost_query);
        $lost_stmt->execute();
        $public_lost_items = $lost_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $public_lost_items = [];
    }

    try {
        $general_query = "SELECT * FROM general_lost_items 
                         WHERE status IN ('pending', 'investigating')
                         AND public_visibility = 1
                         ORDER BY reported_date DESC
                         LIMIT 50";
        $general_stmt = $db->prepare($general_query);
        $general_stmt->execute();
        $public_general_lost = $general_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $public_general_lost = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Lost Items - PEARTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .container {
            padding: 40px 20px;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        .lost-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
            cursor: pointer;
            border-left: 4px solid #dc3545;
        }
        .lost-card:hover {
            transform: translateX(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .general-card {
            border-left-color: #ffc107;
        }
        .btn-back {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="index.php" class="btn-back mb-3 d-inline-block">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
            <h1><i class="bi bi-exclamation-triangle"></i> Public Lost Items</h1>
            <p>These items have been reported lost and made public by their owners</p>
        </div>

        <?php if (empty($public_lost_items) && empty($public_general_lost)): ?>
            <div class="text-center text-white py-5">
                <i class="bi bi-inbox fs-1"></i>
                <h3>No public lost items</h3>
                <p>There are no lost items currently posted publicly.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($public_lost_items as $item): ?>
                    <div class="col-md-6">
                        <div class="lost-card" onclick="window.location.href='views/lost/public_view.php?id=<?php echo $item['lost_id']; ?>&type=asset'">
                            <h5><?php echo htmlspecialchars($item['brand'] . ' ' . $item['model']); ?></h5>
                            <p class="text-muted small">Serial: <?php echo htmlspecialchars($item['serial_number']); ?></p>
                            <p><i class="bi bi-geo-alt"></i> Last seen: <?php echo htmlspecialchars($item['last_seen_location']); ?></p>
                            <p><i class="bi bi-calendar"></i> Reported: <?php echo date('d M Y', strtotime($item['reported_date'])); ?></p>
                            <span class="badge bg-warning"><?php echo ucfirst($item['status']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($public_general_lost as $item): ?>
                    <div class="col-md-6">
                        <div class="lost-card general-card" onclick="window.location.href='views/lost/public_view.php?id=<?php echo $item['item_id']; ?>&type=general'">
                            <h5><?php echo htmlspecialchars($item['item_type']); ?></h5>
                            <p class="text-muted small"><?php echo htmlspecialchars(substr($item['item_description'], 0, 60)); ?></p>
                            <p><i class="bi bi-geo-alt"></i> Last seen: <?php echo htmlspecialchars($item['last_seen_location']); ?></p>
                            <p><i class="bi bi-calendar"></i> Reported: <?php echo date('d M Y', strtotime($item['reported_date'])); ?></p>
                            <span class="badge bg-warning"><?php echo ucfirst($item['status']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>