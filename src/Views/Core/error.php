<?php
$Error = false;
if (isset($arguments)) {
    if (isset($arguments['errors'])) {
        $Error = $arguments['errors'];
    }
}

ob_start();
?>

<?php if ($Error): ?>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle me-3" viewBox="0 0 16 16">
                    <path d="M7.938 2.016a1.13 1.13 0 0 1 .938 0l6.857 3.943a1.13 1.13 0 0 1 0 1.974l-6.857 3.943a1.13 1.13 0 0 1-.938 0L1.081 7.933a1.13 1.13 0 0 1 0-1.974L7.938 2.016zM8 3.646L2.172 6.928l5.828 3.282 5.828-3.282L8 3.646zm-.846 5.31a.652.652 0 1 0 1.302 0l-.204-2.717a.448.448 0 0 0-.894 0l-.204 2.717zm.846 1.78a.665.665 0 1 0-1.33 0 .665.665 0 0 0 1.33 0z"/>
                </svg>
                <div>
                    <?= $Error ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
