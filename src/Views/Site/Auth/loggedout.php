<?php
if (isset($arguments)) {
    if (isset($arguments['message'])) {
        $message = $arguments['message'];
    }

    if(isset($arguments['message_header'])){
        $message_header = $arguments['message_header'];
    }
}

ob_start();

?>


<div class="alert alert-info text-center mt-4" role="alert">
    <h4 class="alert-heading"> <?= $message_header ?> </h4>
    <p><?= $message ?></p>
    <a href="/User/Login" class="btn btn-primary mt-3">Log back in</a>
</div>


<?php
$template = ob_get_contents();
ob_end_clean();
