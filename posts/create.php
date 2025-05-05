<?php
// Start the session first thing
session_start();

// Detect whether you're on localhost or live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // Add a leading slash for absolute path
    $base_url = '/avengers_blog';
} else {
    $base_url = '/ics325/students/2025/TRana';
}

$public_url = $base_url . "/public";
$images_url = $base_url . "/assets/images";
$js_url = $base_url . "/assets/js";
$css_url = $base_url . "/assets/css";

// Include database connection - use document root for absolute path
include($_SERVER['DOCUMENT_ROOT'] . $base_url . "/config/database.php");

// Path setup - use document root for absolute path
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";
$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";

// Make sure user is logged in
if (!isset($_SESSION['valid_user'])) {
    header("Location: " . $base_url . "/auth/login.php");
    exit;
}

// Get the user ID from the session username
function getUserId($username, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

// Initialize variables
$errors = [];
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim(filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS));
    $post = trim(filter_input(INPUT_POST, "post", FILTER_SANITIZE_SPECIAL_CHARS));
    $current_datetime = date('Y-m-d H:i:s');
    
    if (empty($title)) {
        $errors['title'] = "Title is required";
    }
    if (empty($post)) {
        $errors['post'] = "Post content is required";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $userId = getUserId($_SESSION['valid_user'], $pdo);

            if (!$userId) {
                throw new Exception("Could not identify the current user.");
            }

            $query = $pdo->prepare("INSERT INTO posts (topic_id, created_by, title, content, date_posted) 
                VALUES (:user_id, :title, :content, :date_posted)");
            $query->execute([
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
                "date_posted" => $date_posted
            ]);

            $pdo->commit();
            header("Location: " . $base_url . "/posts/index.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log($e->getMessage());
            $message = '<div class="alert alert-danger">Post submission failed. Please try again later.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Blog Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $css_url; ?>/styles.css">
    <script src="<?php echo $js_url; ?>/script.js"></script>
</head>
<body>
    <?php include($header); ?>

    <div class="main-content">
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h1 class="h3 mb-0">Create Blog Post</h1>
                        </div>

                        <div class="card-body">
                            <p class="mb-3 text-muted">Posting as: <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['valid_user']); ?></span></p>
                            <?php if (!empty($message)) echo $message; ?>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" maxlength="100" required>
                                    <?php if (isset($errors['title'])): ?>
                                        <span class="text-danger"><?php echo $errors['title']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="post" class="form-label">Write your post here</label>
                                    <textarea name="post" id="post" class="form-control" rows="8" required><?php echo htmlspecialchars($_POST['post'] ?? ''); ?></textarea>
                                    <?php if (isset($errors['post'])): ?>
                                        <span class="text-danger"><?php echo $errors['post']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3 text-end">
                                    <button type="submit" class="btn btn-primary">Submit Post</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include($footer); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-8Ft3Hkv0Lw9JwfWg9E9dBPXQf3U8SCxUpZjtGIlXLIHfGzuEEU8/BX1eeaFcC5CFH" crossorigin="anonymous"></script>
</body>
</html>