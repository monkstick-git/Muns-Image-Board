<?php
ob_start();
?>


<!-- <div class="sticky-bottom" style="height: 5em;"> -->
</nav>
</main>

<?php
    # Loop through $cssIncludes and include each CSS file.
    global $jsIncludes;
    foreach ($jsIncludes as $js) {
      echo "<script src=\"$js\" defer></script>\n";
    }
  ?>

</body>
<footer class="container">
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
</footer>
<?php
$template = ob_get_contents();
ob_end_clean();
