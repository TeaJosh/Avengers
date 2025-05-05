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

/*
// Check if $pdo is set after including the database file
if (!isset($pdo)) {
    die("Database connection failed. Please check your database configuration.");
}
*/

// Path setup
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";
$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";

// Get form data
$username = $_POST["Username"] ?? '';
$password = $_POST["Password"] ?? '';
$form_submitted = $_SERVER['REQUEST_METHOD'] == 'POST';
$message = "";
$message_type = ""; // For alert styling
$show_form = true;

// Check if already logged in
if (isset($_SESSION['valid_user'])) {
	$message = "You are logged in as " . $_SESSION['valid_user'] . '<br><a href="logout.php">Logout</a>';
	$message_type = "info";
	$show_form = false;
} else if ($form_submitted) {
	$login_successful = false;
    // Check hard-coded credentials
	if ($username === "user" && $password === "password") {
		$login_successful = true;
	} else {
		try {
			$query = $pdo->prepare("SELECT id, password FROM users WHERE username = :username");
			$query->execute(['username' => $username]);
			
			// echo $query;
			// sleep(100);
			$user = $query->fetch();
			if ($user && password_verify($password, $user['password'])) {
				$login_successful = true;
			}
		} catch (Exception $e) {
			$message = "Database query failure: " . $e->getMessage();
			$message_type = "danger";
		}
	}
	if ($login_successful) {
		$_SESSION['valid_user'] = $username;
		$message = "Login successful";
		$message_type = "success";
		$show_form = false;
	} else {
		$message = !empty($username) ? $username . $password:
		"Could not log you in!" ; "You are not logged in!";
		$message_type = "danger";
	}
}
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
<!-- Section: Design Block -->
<section class="">
	<!-- Jumbotron -->
	<div class="px-4 py-5 px-md-5 text-center text-lg-start">
		<div class="container">
			<div class="row gx-lg-5 align-items-center">
				<!-- Left Column -->
				<div class="col-lg-6 mb-5 mb-lg-0">
					<h1 class="my-5 display-3 fw-bold ls-tight">
						Avengers <br>
						<span class="text-primary">Assemble!</span>
					</h1>
					<p style="color: hsl(217, 10%, 50.8%)">
						Welcome back, Avenger! Please log in below. If you don't have an account, feel free to register.
					</p>
				</div>

				<!-- Right Column -->
				<div class="col-lg-6 mb-5 mb-lg-0">
					<div class="card">
						<div class="card-body py-5 px-md-5">
							<?php if (!empty($message)): ?>
								<div class="alert alert-<?php echo $message_type; ?> text-center" role="alert">
									<?php echo $message; ?>
								</div>
							<?php endif; ?>

							<?php if ($show_form): ?>
								<form action="login.php" method="POST">
									<!-- Username input -->
									<div class="form-outline mb-4">
										<input type="text" id="Username" name="Username" class="form-control" required />
										<label class="form-label" for="Username">Username</label>
									</div>

									<!-- Password input -->
									<div class="form-outline mb-4">
										<input type="password" id="Password" name="Password" class="form-control" required />
										<label class="form-label" for="Password">Password</label>
									</div>

									<!-- Submit button -->
									<button type="submit" class="btn btn-primary btn-block mb-4">Login</button>

									<!-- Register link -->
									<div class="text-center">
										<p>Don't have an account? <a href="register.php">Register</a></p>
									</div>
								</form>
							<?php else: ?>
								<div class="text-center mt-4">
									<a href="../posts/index.php" class="btn btn-outline-primary">Go to Posts</a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include($footer); ?>
</body>
</html>
