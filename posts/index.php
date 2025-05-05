<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

// Detect whether you're on localhost or live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
	$base_url = "/avengers_blog";
} else {
	$base_url = '/ics325/students/2025/TRana';
}

$public_url = $base_url . "/index.php";
$js_url = $base_url . "/assets/js";
$css_url = $base_url . "/assets/css";

// Include database connection - Make sure this file sets up the $pdo variable
require_once($_SERVER['DOCUMENT_ROOT'] . $base_url . "/config/database.php");

// Check if $pdo is set after including the database file
if (!isset($pdo)) {
    die("Database connection failed. Please check your database configuration.");
}

// Path setup
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";
$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="<?php echo $css_url; ?>/style.css">
	<script src="<?php echo $js_url; ?>/script.js"></script>
</head>
<body>
<?php include($header); ?>

<?php include($footer); ?>
</body>
</html>
