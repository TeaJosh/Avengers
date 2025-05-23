<?php
// Detect whether you're on localhost or live server
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
  $base_url = "/avengers_blog";
} else {
  $base_url = '/ics325/students/2025/TRana';
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php echo $css_url; ?>/styles.css">
<script src="<?php echo $js_url; ?>/main.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid d-flex align-items-center">
    <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_url; ?>">
      <img src="<?php echo $base_url; ?>/assets/images/avengers_logo.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-top me-2">
      <span>Avengers</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Left Links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>">Back to Lab</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/home/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/about/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/posts/index.php">Posts</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>/contact/contact.php">Contact</a></li>

        <?php if (isset($_SESSION["valid_user"]) && isset($_SESSION["role"])): ?>
          <?php if ($_SESSION["role"] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/create_post.php">Create Post</a></li>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/create_topic.php">Create Topic</a></li>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/manage_users.php">Manage Users</a></li>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/manage_topics.php">Manage Topics</a></li>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/manage_posts.php">Manage Posts</a></li>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/manage_comments.php">Manage Comments</a></li>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/manage_rankings.php">Manage Rankings</a></li>
          <?php elseif ($_SESSION["role"] === 'poster'): ?>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/create_post.php">Create Post</a></li>
            <li class="nav-item"><a class="nav-link fw-bold" href="<?php echo $base_url; ?>/admin/create_topic.php">Create Topic</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>

      <!-- Right User Menu -->
      <div class="user">
        <?php if (isset($_SESSION["valid_user"])): ?>
          <div class="dropdown">
            <button class="dropdown-toggle btn btn-outline-light" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo htmlspecialchars($_SESSION['valid_user']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="<?php echo $base_url; ?>/users/profile.php">Profile</a></li>
              <li><a class="dropdown-item" href="<?php echo $base_url; ?>/auth/logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?php echo $base_url; ?>/auth/login.php" class="btn btn-primary">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
