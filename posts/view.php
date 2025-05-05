<?php
session_start();

// Detect whether you're on localhost or the live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $base_url = '/ICS325/homework/portfolio';
} else {
    $base_url = '/ics325/students/2025/TRana';
}

$public_url = $base_url . "/public";
$images_url = $base_url . "/images";
$js_url = $base_url . "/js";
$css_url = $base_url . "/css";
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";

// Include layout parts
$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";
// Include database connection
include($_SERVER['DOCUMENT_ROOT'] . $base_url . "/config/database.php");

// Check if user is logged in
if (!isset($_SESSION['valid_user'])) {
    header("Location: login.php");
    exit;
}

// Get blog posts
$posts = [];
try {
    // Select from table name: posts
    $query = $pdo->query("SELECT posts.*, u.username 
       FROM posts p
       JOIN users u ON p.user_id = u.id
       ORDER BY p.date_posted DESC");
    $posts = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle error silently or display a message
    $error_message = "Could not fetch blog posts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Only Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $css_url; ?>/styles.css">
</head>
<body>
    <?php include($header); ?>
    
    <div class="main-content">
        <div class="container mt-4">
            <h1 class="mb-4">Members Area</h1>
            
            <div class="mb-4">
                <a href="create.php" class="btn btn-primary">Create a new post</a>
            </div>
            
            <?php if (!empty($posts)): ?>
                <h2 class="mb-3">Recent Posts</h2>
                <?php foreach ($posts as $item): ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="h5 mb-0"><?php echo htmlspecialchars($item['title']); ?></h3>
                        </div>

                        <div class="card-body">
                            <div class="text-muted mb-3 small">
                                By <?php echo htmlspecialchars($item['username']); ?> 
                                <?php if (isset($item['date_posted']) && $item['date_posted']): ?>
                                    on <?php echo date('g:ia \o\n l jS F Y', strtotime($item['date_posted'])); ?>
                                <?php else: ?>
                                    on unknown date
                                <?php endif; ?>
                            </div>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($item['body'])); ?></p>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    No posts yet. Be the first to create one!
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-8Ft3Hkv0Lw9JwfWg9E9dBPXQf3U8SCxUpZjtGIlXLIHfGzuEEU8/BX1eeaFcC5CFH" crossorigin="anonymous"></script>
    <script src="<?php echo $js_url; ?>/script.js"></script>
    <?php include($footer); ?>
</body>
</html>
