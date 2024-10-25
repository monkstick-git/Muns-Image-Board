<?php
ob_start();
?>


<!-- <div class="sticky-bottom" style="height: 5em;"> -->
</nav>
</main>

<?php
    # Loop through $cssIncludes and include each CSS file.
    foreach (Registry::get("jsIncludes") as $js) {
      echo "<script src=\"$js\" defer></script>\n";
    }
  ?>


  <!-- Footer section -->
  <footer class="fixed-bottom navbar navbar-expand-md navbar-dark bg-dark">
    <nav class="container">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="<?=Registry::get("RouteTranslations")['GalleryPage'];?>">Gallery</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=Registry::get("RouteTranslations")['AboutPage'];?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://github.com/monkstick-git/Muns-Image-Board">Source</a>
        </li>
      </ul>
    </nav>
  </footer>
  </body>
<?php
$template = ob_get_contents();
ob_end_clean();
