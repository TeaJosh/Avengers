<?php
// Detect whether you're on localhost or live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
  $base_url = "/avengers_blog";
} else {
  $base_url = '/ics325/students/2025/TRana';
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="<?php echo $base_url; ?>/styles.css">
  <script src="<?php echo $base_url; ?>/main.js"></script>
  <title>Avengers Blog</title>
</head>
<body class="d-flex flex-column min-vh-100">
  <!-- Footer -->
  <footer class="bg-dark text-white text-center text-lg-start mt-auto">
    <!-- Grid container -->
    <div class="container p-4">
      <!--Grid row-->
      <div class="row">
        <!--Grid column-->
        <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
          <h5 class="text-uppercase" style="color: #5584AC">Footer Content</h5>
          <p>
            Welcome to the official Avengers Blog, your ultimate source for superhero news, mission updates, and behind-the-scenes content from Earth's Mightiest Heroes. Join us as we document our adventures, share training tips, and keep you informed about the latest threats to global security.
          </p>
          </p>
        </div>
        <!--Grid column-->
        <!--Grid column-->
        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
          <h5 class="text mb-0">Contact Us</h5>
          <ul class="list-unstyled mb-0">
            <li>
              <a href="#!" class="text-white">Link 1</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 2</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 3</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 4</a>
            </li>
          </ul>
        </div>
        <!--Grid column-->
        <!--Grid column-->
        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
          <h5 class="text mb-0">Newsletter</h5>
          <ul class="list-unstyled">
            <li>
              <a href="#!" class="text-white">Link 1</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 2</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 3</a>
            </li>
            <li>
              <a href="#!" class="text-white">Link 4</a>
            </li>
          </ul>
        </div>
        <!--Grid column-->
      </div>
      <!--Grid row-->
    </div>
    <!-- Grid container -->
    <!-- Copyright and Social Media Row -->
    <div class="bg-black p-3">
      <div class="container">
        <div class="row d-flex align-items-center">
          <div class="col-md-7 col-lg-8 text-md-start">
            <div>© 2025 Copyright: Avengers</div>
          </div>
          <div class="col-md-5 col-lg-4 text-md-end">
            <a href="#" class="text-blue me-4 text-decoration-none">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#" class="text-blue me-4 text-decoration-none">
              <i class="bi bi-x"></i>
            </a>
            <a href="#" class="text-blue me-4 text-decoration-none">
              <i class="bi bi-tiktok"></i>
            </a>
            <a href="#" class="text-blue text-decoration-none">
              <i class="bi bi-instagram"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
