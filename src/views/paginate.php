<?php
$page  = $arguments['page'];
$pages = $arguments['pages'];
$url   = $arguments['url'];
#$url = "/Site/Gallary?page=";
ob_start();
?>

<div class="container">
  <div class="row">
    <ul class="pagination justify-content-center row" style="margin: auto">
      <?php
      for ($i = 1; $i <= $pages; $i++) {
        if ($i === $page) { // this is current page
          echo "<li class='col page-item active'>
          <a class='page-link' href='$url$i'>$i<span class='sr-only'>(current)</span></a>
          </li>";
        } else { // show link to other page   
          echo "<li class='col page-item'>
          <a class='page-link' href='$url$i'>$i</a>
          </li>";
        }
      }
      ?>
  </div>
</div>
</ul>

<?php
$template = ob_get_contents();
ob_end_clean();
