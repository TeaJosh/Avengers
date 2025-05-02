<?php
// Detect whether you're on localhost or the live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
  $base_url = '/avengers_blog';
} else {
  $base_url = '/ics325/students/2025/TRana';
}

$public_url = $base_url . "/public";
$images_url = $base_url . "/assets/images"; // Fixed path to match your directory structure
$js_url = $base_url . "/assets/js"; // Fixed path to match your directory structure
$css_url = $base_url . "/assets/css"; // Fixed path to match your directory structure
$includes_path = $_SERVER["DOCUMENT_ROOT"] . $base_url . "/includes";

$header = $includes_path . "/header.php";
$footer = $includes_path . "/footer.php";

// Include layout parts
$included_from_root = true;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <!-- Added Font Awesome -->
  <link rel="stylesheet" href="<?php echo $css_url; ?>/style.css">
  <script src="<?php echo $js_url; ?>/main.js"></script>
  <title>About</title>
</head>
<body>
  <?php include($header); ?>
  <div class="container">
    <h1 class="my-5 text-center"><b>About the Avengers</b></h1>

    <!-- Section: Introduction -->
    <section class="about-section mb-5">
      <h2>Who Are The Avengers?</h2>
      <p>The Avengers are a group of extraordinary heroes assembled to defend Earth from alien invaders, rogue gods, and other world-threatening forces. Each member of the team brings their unique abilities and skills to tackle challenges that no one hero could face alone.</p>

      <!-- Card grid for Avengers team members -->
      <div class="row row-cols-1 row-cols-md-3 g-4 mt-4">
        <!-- Iron Man Card -->
        <div class="col">
          <div class="card h-100">
            <img src="<?php echo $images_url; ?>/heros/iron-man.jpg" class="card-img-top" alt="Iron Man" onerror="this.onerror=null; this.src='<?php echo $images_url; ?>/placeholder.jpg'">
            <div class="card-body">
              <h5 class="card-title">Iron Man</h5>
              <p class="card-text">Genius billionaire Tony Stark uses his advanced armor to protect the world as Iron Man.</p>
            </div>
          </div>
        </div>

        <!-- Captain America Card -->
        <div class="col">
          <div class="card h-100">
            <img src="<?php echo $images_url; ?>/heros/captain-america.jpg" class="card-img-top" alt="Captain America" onerror="this.onerror=null; this.src='<?php echo $images_url; ?>/placeholder.jpg'">
            <div class="card-body">
              <h5 class="card-title">Captain America</h5>
              <p class="card-text">Super-soldier Steve Rogers leads the Avengers with unwavering courage and tactical brilliance.</p>
            </div>
          </div>
        </div>

        <!-- Thor Card -->
        <div class="col">
          <div class="card h-100">
            <img src="<?php echo $images_url; ?>/heros/thor.jpg" class="card-img-top" alt="Thor" onerror="this.onerror=null; this.src='<?php echo $images_url; ?>/placeholder.jpg'">
            <div class="card-body">
              <h5 class="card-title">Thor</h5>
              <p class="card-text">The God of Thunder wields the mighty Mjolnir to protect both Asgard and Earth.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section: Famous Quote -->
    <section class="about-section mb-5">
      <h2>Famous Quote</h2>
      <div class="card mb-4">
        <div class="card-body bg-light">
          <blockquote class="blockquote text-center mb-0">
            <p>"I am Iron Man." - Tony Stark</p>
          </blockquote>
        </div>
      </div>
      <p>This iconic line represents Tony Stark's transformation from a self-centered billionaire to a hero willing to sacrifice everything for the greater good. It's one of the most memorable moments in the MCU and embodies the theme of redemption in the Avengers series.</p>
    </section>

    <!-- Section: Why We Love The Avengers -->
    <section class="about-section mb-5">
      <h2>Why We Love The Avengers</h2>
      <p>The Avengers stand for more than just fighting villains—they represent teamwork, resilience, and sacrifice.</p>

      <!-- Card grid for reasons -->
      <div class="row row-cols-1 row-cols-md-2 g-4 mt-4">
        <!-- Teamwork Card -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title"><i class="fa fa-users text-primary me-2"></i>Teamwork</h5>
              <p class="card-text">The Avengers prove that collaboration makes the impossible possible.</p>
            </div>
          </div>
        </div>

        <!-- Resilience Card -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title"><i class="fa fa-shield text-danger me-2"></i>Resilience</h5>
              <p class="card-text">No matter the loss, they always rise to face the next challenge.</p>
            </div>
          </div>
        </div>

        <!-- Relatable Characters Card -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title"><i class="fa fa-heart text-success me-2"></i>Relatable Characters</h5>
              <p class="card-text">Each Avenger has flaws and struggles, making them human despite their extraordinary abilities.</p>
            </div>
          </div>
        </div>

        <!-- Epic Battles Card -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title"><i class="fa fa-bolt text-warning me-2"></i>Epic Battles</h5>
              <p class="card-text">From the Battle of New York to the final showdown in Endgame, the Avengers have given us some of the most thrilling battles in cinema history.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section: The Legacy of the Avengers -->
    <section class="about-section">
      <h2>The Legacy of the Avengers</h2>
      <div class="card mb-4">
        <div class="row g-0">
          <div class="col-md-4">
            <img src="<?php echo $images_url; ?>/legacy.jpg" class="img-fluid rounded-start" alt="Avengers Legacy" onerror="this.onerror=null; this.src='<?php echo $images_url; ?>/placeholder.jpg'">
          </div>
          <div class="col-md-8">
            <div class="card-body">
              <h5 class="card-title">A Lasting Impact</h5>
              <p class="card-text">As one of the most successful franchises in cinematic history, the Avengers have left an indelible mark on pop culture. They introduced audiences to new heroes, tackled complex themes, and delivered some of the most groundbreaking visual effects ever seen on screen.</p>
              <p class="card-text">Even after the events of "Avengers: Endgame," the legacy of the Avengers continues to inspire new generations of fans around the world.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
<?php include($footer); ?>
</body>
</html>
