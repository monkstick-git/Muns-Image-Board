<?php
$page = $arguments['page'];
$pages = $arguments['pages'];
$url = $arguments['url'];

ob_start();

$range = 2; // Range of pages to show around the current page
$start = max(1, $page - $range);
$end = min($pages, $page + $range);
?>

<div class="container">
    <div class="row">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">

                <!-- Previous Button -->
                <li class="page-item <?= ($page == 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?= ($page > 1) ? $url . ($page - 1) : '#'; ?>" tabindex="-1"><</a>
                </li>

                <!-- First Page and Ellipsis -->
                <?php if ($start > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $url . '1'; ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= ($i === $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?= $url . $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Last Page and Ellipsis -->
                <?php if ($end < $pages): ?>
                    <?php if ($end < $pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $url . $pages; ?>"><?= $pages; ?></a>
                    </li>
                <?php endif; ?>

                <!-- Next Button -->
                <li class="page-item <?= ($page == $pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?= ($page < $pages) ? $url . ($page + 1) : '#'; ?>">></a>
                </li>

            </ul>
        </nav>
    </div>
</div>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
