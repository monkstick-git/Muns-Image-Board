<?php
$page = $arguments['page'];
$pages = $arguments['pages'];
$url = $arguments['url'];

ob_start();
?>

<div class="container">
  <div class="row">
    <ul class="pagination justify-content-center row" style="margin: auto">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <?php if ($i === $page): ?>
          <li class='col page-item active'>
            <a class='page-link' href='<?= $url . $i ?>'><?= $i ?><span class='sr-only'>(current)</span></a>
          </li>
        <?php else: ?>
          <li class='col page-item'>
            <a class='page-link' href='<?= $url . $i ?>'><?= $i ?></a>
          </li>
        <?php endif; ?>
      <?php endfor; ?>
  </div>
</div>
</ul>

<?php
$template = ob_get_contents();
ob_end_clean();
