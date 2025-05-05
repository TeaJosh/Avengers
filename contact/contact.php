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

// Path setup
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";
$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";

// Form handling variables
$form_submitted = $_SERVER['REQUEST_METHOD'] == 'POST';
$message = "";
$message_type = ""; // For alert styling

// Process form submission
if ($form_submitted) {
    // Basic validation
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $content = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($subject) || empty($content)) {
        $message = "Please fill out all fields";
        $message_type = "danger";
    } else {
        // Set email recipient
        $recipient = "tejoshrana@gmail.com";
        
        // Create email headers
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Compose email body
        $email_body = "<html><body>";
        $email_body .= "<h2>Contact Form Submission</h2>";
        $email_body .= "<p><strong>Name:</strong> $name</p>";
        $email_body .= "<p><strong>Email:</strong> $email</p>";
        $email_body .= "<p><strong>Subject:</strong> $subject</p>";
        $email_body .= "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($content)) . "</p>";
        $email_body .= "</body></html>";
        
        // Send email
        if (mail($recipient, "Contact Form: $subject", $email_body, $headers)) {
            $message = "Your message has been sent successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to send your message. Please try again later.";
            $message_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Contact Us</title>
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
						Contact <br>
						<span class="text-primary">The Avengers</span>
					</h1>
					<p style="color: hsl(217, 10%, 50.8%)">
						Have a question or need to reach the team? Send us a message and we'll get back to you as soon as possible.
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

							<form action="contact.php" method="POST">
								<!-- Name input -->
								<div class="form-outline mb-4">
									<input type="text" id="name" name="name" class="form-control" required />
									<label class="form-label" for="name">Name</label>
								</div>

								<!-- Email input -->
								<div class="form-outline mb-4">
									<input type="email" id="email" name="email" class="form-control" required />
									<label class="form-label" for="email">Email address</label>
								</div>
                                
                                <!-- Subject input -->
								<div class="form-outline mb-4">
									<input type="text" id="subject" name="subject" class="form-control" required />
									<label class="form-label" for="subject">Subject</label>
								</div>

								<!-- Message input -->
								<div class="form-outline mb-4">
									<textarea class="form-control" id="message" name="message" rows="4" required></textarea>
									<label class="form-label" for="message">Message</label>
								</div>

								<!-- Submit button -->
								<button type="submit" class="btn btn-primary btn-block mb-4">Send Message</button>
							</form>
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
