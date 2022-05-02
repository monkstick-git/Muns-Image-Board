<?php

$page  = $arguments['page'];
$pages = $arguments['pages'];
$template = '
<div class="container">
<div class="row">
<ul class="pagination justify-content-center row" style="margin: auto">
';

for ($i = 1; $i <= $pages; $i++) {
  if ($i === $page) { // this is current page
    $template .= "<li class='col page-item active'><a class='page-link' href='/Site/Gallary?page=$i'>$i<span class='sr-only'>(current)</span></a></li>";
  } else { // show link to other page   
    $template .= "<li class='col page-item'><a class='page-link' href='/Site/Gallary?page=$i'>$i</a></li>";
      #$template .= ('<a href="/Site/Gallary?page=' . $i . '">Page ' . $i . '</a><br>');
  }
}

$template .= "
</div>
</div>
</ul>
";
