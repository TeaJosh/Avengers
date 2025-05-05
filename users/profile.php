<?php
// Start the session first thing
session_start();

// Detect whether you're on localhost or live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
	$base_url = '/ICS325/homework/portfolio';
} else {
	$base_url = '/ics325/students/2025/TRana';
}

$public_url = $base_url . "/public";
$images_url = $base_url . "/images";
$js_url = $base_url . "/js";
$css_url = $base_url . "/css";

// Include database connection
include($_SERVER['DOCUMENT_ROOT'] . $base_url . "/userdb.php");

// Path setup
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";
$header = $includes_path . "/header.php";
$menu = $includes_path . "/menu.php";
$footer = $includes_path . "/footer.php";

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['valid_user'])) {
	header("Location: login.php");
	exit();
}

// Get current user's info from the database
$username = $_SESSION['valid_user'];
$user_id = null;

try {
	$query = $pdo->prepare("SELECT id FROM users WHERE username = :username");
	$query->execute(['username' => $username]);
	$user = $query->fetch();
	if ($user) {
		$user_id = $user['id'];
	} else {
        // If user ID not found, redirect to login
		header("Location: login.php");
		exit();
	}
} catch (Exception $e) {
	die("Database error: " . $e->getMessage());
}

// Initialize variables
$first_name = $last_name = $email = $bio = $address = $phone = $occupation = $city = $state = "";
$profile_image = "default_profile.jpg"; // Default image
$success_message = $error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$email = trim($_POST['email']);
	$bio = trim($_POST['bio']);
	$address = trim($_POST['address']);
	$phone = trim($_POST['phone']);
	$occupation = trim($_POST['occupation']);
	$city = trim($_POST['city']);
	$state = trim($_POST['state']);

    // Validate inputs
	if (empty($first_name) || empty($last_name) || empty($email)) {
		$error_message = "First name, last name, and email are required fields.";
	} else {
        // Handle profile image upload
		if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
			$allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
			$file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

			if (in_array($file_extension, $allowed_types)) {
				$upload_dir = $_SERVER['DOCUMENT_ROOT'] . $base_url . "/uploads/";

                // Create the uploads directory if it doesn't exist
				if (!file_exists($upload_dir)) {
					mkdir($upload_dir, 0755, true);
				}

				$new_file_name = $user_id . "_" . time() . "." . $file_extension;
				$upload_path = $upload_dir . $new_file_name;

				if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
					$profile_image = $new_file_name;
				} else {
					$error_message = "Failed to upload image. Please try again.";
				}
			} else {
				$error_message = "Only JPG, JPEG, PNG, GIF and WEBP files are allowed.";
			}
		}

		if (empty($error_message)) {
			try {
                // Check if profile already exists
				$check_query = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
				$check_query->execute(['user_id' => $user_id]);

				if ($check_query->rowCount() > 0) {
                    // Update existing profile
					$stmt = $pdo->prepare("UPDATE user_profiles SET first_name = :first_name, last_name = :last_name, 
						email = :email, bio = :bio, address = :address, phone = :phone, 
						profile_image = :profile_image, occupation = :occupation, city = :city, state = :state
						WHERE user_id = :user_id");
				} else {
                    // Create new profile
					$stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, first_name, last_name, email, bio, 
						address, phone, profile_image, occupation, city, state) 
					VALUES (:user_id, :first_name, :last_name, :email, :bio, :address, 
						:phone, :profile_image, :occupation, :city, :state)");
				}

				$stmt->execute([
					'user_id' => $user_id,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $email,
					'bio' => $bio,
					'address' => $address,
					'phone' => $phone,
					'profile_image' => $profile_image,
					'occupation' => $occupation,
					'city' => $city,
					'state' => $state
				]);

				$success_message = "Profile updated successfully!";
			} catch (PDOException $e) {
				$error_message = "Error updating profile: " . $e->getMessage();
			}
		}
	}
}

// Get existing profile data if it exists
try {
	$query = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
	$query->execute(['user_id' => $user_id]);
	$profile_data = $query->fetch(PDO::FETCH_ASSOC);

	if ($profile_data) {
		$first_name = $profile_data['first_name'];
		$last_name = $profile_data['last_name'];
		$email = $profile_data['email'];
		$bio = $profile_data['bio'];
		$address = $profile_data['address'];
		$phone = $profile_data['phone'];
		$profile_image = $profile_data['profile_image'];
		$occupation = $profile_data['occupation'] ?? '';
		$city = $profile_data['city'] ?? '';
		$state = $profile_data['state'] ?? '';
	}
} catch (PDOException $e) {
	$error_message = "Error fetching profile data: " . $e->getMessage();
}

// Placeholder data for profile stats
$photos_count = 253;
$followers_count = 1026;
$following_count = 478;

// Get display name
$display_name = $first_name . ' ' . $last_name;
if (empty(trim($display_name))) {
	$display_name = $username;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Move Bootstrap JS bundle here -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $css_url; ?>/styles.css">
    <script src="<?php echo $js_url; ?>/script.js"></script>
	<style>
		.gradient-custom-2 {
			background-color: #f8f9fa;
			padding: 20px 0;
		}
		.profile-card {
			max-width: 100%;
			margin: 0 auto;
		}
		.profile-header {
			background-color: #000;
			height: 200px;
			position: relative;
		}
		.profile-image-container {
			position: relative;
			z-index: 1;
		}
		.profile-stats {
			border-top: 1px solid #eee;
			border-bottom: 1px solid #eee;
		}
		.recent-photos img {
			height: 200px;
			object-fit: cover;
		}
		@media (max-width: 767px) {
			.profile-header {
				height: 250px;
			}
		}
	</style>
</head>
<body>
	<?php include($header); ?>
	<?php include($menu); ?>

	<div class="container-fluid py-4">
		<div class="row justify-content-center">
			<div class="col-lg-10 col-md-11 col-12">
				<?php if (!empty($success_message)): ?>
					<div class="alert alert-success mb-4"><?php echo $success_message; ?></div>
				<?php endif; ?>

				<?php if (!empty($error_message)): ?>
					<div class="alert alert-danger mb-4"><?php echo $error_message; ?></div>
				<?php endif; ?>

				<div class="card profile-card shadow-sm">
					<div class="rounded-top text-white d-flex flex-row profile-header">
						<div class="ms-4 mt-5 d-flex flex-column profile-image-container" style="width: 150px;">
							<img src="<?php echo $base_url; ?>/uploads/<?php echo $profile_image; ?>"
							alt="Profile Image" class="img-fluid img-thumbnail mt-4 mb-2"
							style="width: 150px; z-index: 1">
							<button type="button" class="btn btn-outline-dark bg-light text-body mt-2" 
								data-bs-toggle="modal" data-bs-target="#editProfileModal" style="z-index: 1;">
								Edit profile
							</button>
						</div>

						<div class="ms-3" style="margin-top: 130px;">
							<h5><?php echo htmlspecialchars($display_name); ?></h5>
							<p><?php echo htmlspecialchars($city); ?></p>
						</div>
					</div>

					<!-- Stats Section -->
					<div class="p-4 text-black bg-body-tertiary profile-stats">
						<div class="d-flex justify-content-center text-center py-1 text-body">
							<div>
								<p class="mb-1 h5"><?php echo $photos_count; ?></p>
								<p class="small text-muted mb-0">Photos</p>
							</div>

							<div class="px-3">
								<p class="mb-1 h5"><?php echo $followers_count; ?></p>
								<p class="small text-muted mb-0">Followers</p>
							</div>

							<div>
								<p class="mb-1 h5"><?php echo $following_count; ?></p>
								<p class="small text-muted mb-0">Following</p>
							</div>
						</div>
					</div>

					<div class="card-body p-4 text-black">
						<div class="mb-5 text-body">
							<p class="lead fw-normal mb-1">About</p>
							<div class="p-4 bg-body-tertiary rounded">
								<?php if (!empty($occupation)): ?>
									<p class="font-italic mb-1"><?php echo htmlspecialchars($occupation); ?></p>
								<?php endif; ?>

								<?php if (!empty($city) && !empty($state)): ?>
									<p class="font-italic mb-1">Lives in <?php echo htmlspecialchars($city . ', ' . $state, ENT_QUOTES); ?></p>
								<?php endif; ?>

								<?php if (!empty($bio)): ?>
									<p class="font-italic mb-0"><?php echo htmlspecialchars($bio); ?></p>
								<?php endif; ?>
							</div>
						</div>

						<div class="d-flex justify-content-between align-items-center mb-4 text-body">
							<p class="lead fw-normal mb-0">Recent posts</p>
							<p class="mb-0"><a href="#!" class="text-muted">Show all</a></p>
						</div>

						<!-- Photo gallery - responsive grid layout -->
						<div class="row g-3 recent-photos">
							<div class="col-md-6 col-lg-3">
								<img src="<?php echo $images_url; ?>/placeholder1.jpg" alt="Photo placeholder"
								class="w-100 rounded-3 shadow-sm" onerror="this.src='<?php echo $base_url; ?>/images/placeholder.jpg'">
							</div>

							<div class="col-md-6 col-lg-3">
								<img src="<?php echo $images_url; ?>/placeholder2.jpg" alt="Photo placeholder"
								class="w-100 rounded-3 shadow-sm" onerror="this.src='<?php echo $base_url; ?>/images/placeholder.jpg'">
							</div>

							<div class="col-md-6 col-lg-3">
								<img src="<?php echo $images_url; ?>/placeholder3.jpg" alt="Photo placeholder"
								class="w-100 rounded-3 shadow-sm" onerror="this.src='<?php echo $base_url; ?>/images/placeholder.jpg'">
							</div>
							
							<div class="col-md-6 col-lg-3">
								<img src="<?php echo $images_url; ?>/placeholder4.jpg" alt="Photo placeholder"
								class="w-100 rounded-3 shadow-sm" onerror="this.src='<?php echo $base_url; ?>/images/placeholder.jpg'">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Edit Profile Modal -->
	<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>

					<div class="modal-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<label for="first_name" class="form-label">First Name</label>
								<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
							</div>

							<div class="col-md-6">
								<label for="last_name" class="form-label">Last Name</label>
								<input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="email" class="form-label">Email</label>
								<input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
							</div>

							<div class="col-md-6">
								<label for="phone" class="form-label">Phone Number</label>
								<input type="tel" class="form-control" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="occupation" class="form-label">Occupation</label>
								<input type="text" class="form-control" name="occupation" id="occupation" value="<?php echo htmlspecialchars($occupation); ?>">
							</div>

							<div class="col-md-6">
								<label for="city" class="form-label">City</label>
								<input type="text" class="form-control" name="city" id="city" value="<?php echo htmlspecialchars($city); ?>">
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="state" class="form-label">State</label>
								<input type="text" class="form-control" name="state" id="state" value="<?php echo htmlspecialchars($state); ?>">
							</div>

							<div class="col-md-6">
								<label for="address" class="form-label">Address</label>
								<input type="text" class="form-control" name="address" id="address" value="<?php echo htmlspecialchars($address); ?>">
							</div>
						</div>

						<div class="mb-3">
							<label for="bio" class="form-label">Bio</label>
							<textarea class="form-control" name="bio" id="bio" rows="3"><?php echo htmlspecialchars($bio); ?></textarea>
						</div>

						<div class="mb-3">
							<label for="profile_image" class="form-label">Profile Image</label>
							<input type="file" class="form-control" name="profile_image" id="profile_image">
							<div class="form-text">Recommended size: 150x150 pixels</div>
						</div>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php include($footer); ?>

	<!-- Bootstrap JS Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
