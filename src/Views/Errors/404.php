<?php
$Error = false;
if (isset($arguments)) {
    if (isset($arguments['errors'])) {
        $Error = $arguments['errors'];
    }
    if (isset($arguments['success'])) {
        $Success = $arguments['success'];
    }
}

ob_start();
?>

<?php if ($Error): ?>
    <div class="alert alert-warning" role="alert">
        <?= $Error ?>
    </div>
<?php endif; ?>

<?php
$template = ob_get_contents();
ob_end_clean();
?>