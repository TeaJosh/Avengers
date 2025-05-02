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
  <link rel="stylesheet" href="<?php echo $css_url; ?>/styles.css">
  <script src="<?php echo $js_url; ?>/main.js"></script>
  <title>Home</title>
</head>
<body>
<?php include($header); ?>
<div id="content" class="container">
  <h1>Front-End Student</h1>
  <p>I am a student of the front-end world. I am currently learning PHP, SQL, Bootstrap, and AJAX. I look forward to connecting with you.</p>
  <form action="<?= $base_url ?>/about/about.php" method="get">
    <button type="submit" class="btn btn-success">About Me</button>
  </form>
</div>
<?php include($footer); ?>
</body>
</html>
