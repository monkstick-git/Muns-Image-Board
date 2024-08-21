<?php
ob_start();
?>

<!-- 
  Hack to force the footer to have a space between content and footer.
 -->
<style>
  @media (max-width: 767px) {
  body {
      padding-bottom: 60px; /* or any height that suits your footer */
  }
}

</style>
<div class="sticky-bottom" style="height: 5em;">
  </nav>
  </main>
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
