<?php
# Display Footer
ob_start();
?>

<nav class="fixed-bottom navbar navbar-expand-md navbar-dark bg-dark">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" href="/Site/Gallary">Gallary</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/Site/About">About</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="https://github.com/monkstick-git/Muns-Image-Board">Source</a>
    </li>
</nav>

<?php
$template = ob_get_contents();
ob_end_clean();
