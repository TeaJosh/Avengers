<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
if (strpos($host, 'localhost') !== false) {
  $folder = "/avengers_blog";
} else {
  $folder = "/ics325/students/2025/TRana";
}
$base_url = "$protocol://$host$folder";
$public_url = $base_url . "/public";
$images_url = $base_url . "/images";
$js_url = $base_url . "/js";
$css_url = $base_url . "/css";
$includes_path = $_SERVER["DOCUMENT_ROOT"] . "/avengers_blog/includes";  
$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="<?php echo $css_url; ?>/styles.css">
  <script src="<?php echo $js_url; ?>/main.js"></script>
  <title>Avengers Blog - Earth's Mightiest Heroes</title>
</head>
<body>
<?php include($header); ?>

<div class="container-fluid hero-section py-5 mb-4">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h1 class="display-3 fw-bold">Avengers <span class="text-primary">Assemble!</span></h1>
        <p class="lead">Your ultimate source for news, stories, and updates about Earth's Mightiest Heroes.</p>
        <a href="<?php echo $base_url; ?>/posts/index.php" class="btn btn-primary btn-lg mt-3">Read Latest Posts</a>
      </div>
    </div>
  </div>
</div>

<?php include($footer); ?>
</body>
</html>