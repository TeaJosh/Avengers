<?php
/**
 * Registration Page for Avengers Blog
 * 
 * Last updated: 2025-05-04
 * Updated by: Debugging fix
 * 
 * This file handles new user registration with full database integration
 * and proper error handling.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable detailed error reporting (only for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detect environment and set base URL
$is_localhost = strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
$base_url = $is_localhost ? "/avengers_blog" : '/ics325/students/2025/TRana';

// Set up paths
$public_url = $base_url . "/index.php";
$js_url = $base_url . "/assets/js";
$css_url = $base_url . "/assets/css";
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";
$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";
$db_class_path = $_SERVER['DOCUMENT_ROOT'] . $base_url . "/config/database.php";

// Function to verify database prerequisites
function verifyDatabaseSetup() {
    try {
        $root_connection = new PDO("mysql:host=localhost", "root", "");
        $root_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if database exists
        $stmt = $root_connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'tranablog'");
        $db_exists = $stmt->fetch() !== false;
        
        // Check if user exists
        $stmt = $root_connection->query("SELECT User FROM mysql.user WHERE User = 'bloguser'");
        $user_exists = $stmt->fetch() !== false;
        
        // Check user permissions if both exist
        if ($db_exists && $user_exists) {
            $stmt = $root_connection->query("SHOW GRANTS FOR 'bloguser'@'localhost'");
            $has_permissions = false;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (strpos($row[array_key_first($row)], "ON `tranablog`.*") !== false) {
                    $has_permissions = true;
                    break;
                }
            }
            return [
                'success' => $db_exists && $user_exists, // $has_permissions,
                'message' => $db_exists && $user_exists ? // $has_permissions ? 
                    "Database setup verified" : 
                    "Missing configuration: " . 
                    (!$db_exists ? "database " : "") . 
                    (!$user_exists ? "user " : "")
                    // (!$has_permissions ? "permissions" : "")
            ];
        }
        
        return [
            'success' => false,
            'message' => "Database or user missing"
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => "Verification failed: " . $e->getMessage()
        ];
    }
}

// Initialize variables
$username = $_POST["Username"] ?? '';
$password = $_POST["Password"] ?? '';
$confirm_password = $_POST["ConfirmPassword"] ?? '';
$firstname = $_POST["FirstName"] ?? '';
$lastname = $_POST["LastName"] ?? '';
$email = $_POST["Email"] ?? '';
$form_submitted = $_SERVER['REQUEST_METHOD'] == 'POST';
$message = "";
$message_type = "";
$show_form = true;

// Check if already logged in
if (isset($_SESSION['valid_user'])) {
    $message = "You are already logged in as " . $_SESSION['valid_user'] . '<br><a href="logout.php">Logout</a>';
    $message_type = "info";
    $show_form = false;
} else if ($form_submitted) {
    try {
        // Verify database file exists
        if (!file_exists($db_class_path)) {
            throw new Exception("Database configuration not found at: " . $db_class_path);
        }
        require_once($db_class_path);
        
        // Verify database setup if on localhost
        if ($is_localhost) {
            $setup_check = verifyDatabaseSetup();
            if (!$setup_check['success']) {
                throw new Exception($setup_check['message']);
            }
        }

        // Initialize database connection
        // Use consistent credentials - root for localhost or bloguser for production
        if ($is_localhost) {
            $db = new Database("root", "", "tranablog", "localhost");
        } else {
            $db = new Database(); // Uses default credentials from the class
        }
        
        // Verify tables exist
        $tables_check = $db->verifyTables();
        if (!$tables_check['success']) {
            $missing_tables = implode(", ", $tables_check['missing']);
            throw new Exception("Missing required tables: $missing_tables");
        }
        
        // Validate form inputs
        $errors = [];
        
        // Username validation
        if (empty($username)) {
            $errors[] = "Username is required";
        } else if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Username must be between 3 and 50 characters";
        } else {
            $existingUser = $db->GetUser(null, "username", $username);
            if (!empty($existingUser)) {
                $errors[] = "Username already exists";
            }
        }
        
        // Password validation
        if (empty($password)) {
            $errors[] = "Password is required";
        } else if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        } else if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        // Email validation
        if (empty($email)) {
            $errors[] = "Email is required";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } else {
            $existingEmail = $db->GetUserInfo(null, "email", $email);
            if (!empty($existingEmail)) {
                $errors[] = "Email already exists";
            }
        }
        
        // Process registration if no errors
        if (empty($errors)) {
            // Get default role (3 = normal user)
            $default_role = $db->GetRole(null, 'name', 'user');
            
            // If default role found, use its ID, otherwise fallback to 3
            $role_id = !empty($default_role) ? $default_role[0]['id'] : 3;
            
            // Use enhanced AddUser method with better error handling
            if (method_exists($db, 'EnhancedAddUser')) {
                $userId = $db->EnhancedAddUser($username, $password, $role_id);
            } else {
                // Fallback to regular AddUser
                $userId = $db->AddUser($username, $password, $role_id);
            }
            
            if ($userId) {
                // Add user info
                $userInfoData = [
                    'email' => $email,
                    'fname' => $firstname,
                    'lname' => $lastname
                ];
                
                $addUserInfo = $db->AddUserInfo($userId, $userInfoData);
                
                if ($addUserInfo) {
                    $message = "Registration successful! You can now <a href='login.php'>login</a>.";
                    $message_type = "success";
                    $show_form = false;
                } else {
                    // Log detailed error for user_info
                    $error_msg = $db->getLastError() ?: "Could not create user profile";
                    error_log("Failed to add user_info for user ID: $userId. Error: $error_msg");
                    
                    // Rollback if user_info creation fails
                    $db->DeleteUser($userId);
                    
                    throw new Exception($error_msg);
                }
            } else {
                // Get specific error if available
                $error_msg = $db->getLastError() ?: "Could not create user account";
                throw new Exception($error_msg);
            }
        } else {
            $message = "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>";
            $message_type = "danger";
        }
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        $debug_info = $is_localhost ? "<br><br>Debug info: " . $e->getMessage() : "";
        $message = "Registration failed: " . ($is_localhost ? $e->getMessage() : "Please try again later.") . $debug_info;
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | Avengers Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo $css_url; ?>/style.css">
    <script src="<?php echo $js_url; ?>/script.js"></script>
</head>
<body>
<?php include($header); ?>
<section class="">
    <div class="px-4 py-5 px-md-5 text-center text-lg-start">
        <div class="container">
            <div class="row gx-lg-5 align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="my-5 display-3 fw-bold ls-tight">
                        Join the <br>
                        <span class="text-primary">Avengers!</span>
                    </h1>
                    <p style="color: hsl(217, 10%, 50.8%)">
                        Create your account to join our community of heroes. Share stories, discuss missions, and connect with fellow Avengers.
                    </p>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="card">
                        <div class="card-body py-5 px-md-5">
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($show_form): ?>
                                <form action="register.php" method="POST">
                                    <!-- Username input -->
                                    <div class="form-outline mb-4">
                                        <input type="text" id="Username" name="Username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required />
                                        <label class="form-label" for="Username">Username</label>
                                    </div>

                                    <!-- Name inputs -->
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="text" id="FirstName" name="FirstName" class="form-control" value="<?php echo htmlspecialchars($firstname); ?>" required />
                                                <label class="form-label" for="FirstName">First name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <input type="text" id="LastName" name="LastName" class="form-control" value="<?php echo htmlspecialchars($lastname); ?>" required />
                                                <label class="form-label" for="LastName">Last name</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email input -->
                                    <div class="form-outline mb-4">
                                        <input type="email" id="Email" name="Email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required />
                                        <label class="form-label" for="Email">Email address</label>
                                    </div>

                                    <!-- Password input -->
                                    <div class="form-outline mb-4">
                                        <input type="password" id="Password" name="Password" class="form-control" required />
                                        <label class="form-label" for="Password">Password</label>
                                    </div>

                                    <!-- Confirm Password input -->
                                    <div class="form-outline mb-4">
                                        <input type="password" id="ConfirmPassword" name="ConfirmPassword" class="form-control" required />
                                        <label class="form-label" for="ConfirmPassword">Confirm password</label>
                                    </div>

                                    <!-- Submit button -->
                                    <button type="submit" class="btn btn-primary btn-block mb-4">
                                        Sign up
                                    </button>

                                    <!-- Login link -->
                                    <div class="text-center">
                                        <p>Already have an account? <a href="login.php">Login</a></p>
                                    </div>
                                </form>
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
