<?php
$Error = false;

if (isset($arguments)) {
    if (isset($arguments['errors'])) {
        $Error = $arguments['errors'];
    }
}

$CSRF = $_SESSION['User']->CSRF->Token;

ob_start();
?>

<link href="/assets/css/signin.css" rel="stylesheet">
<form class="form-signin" action="/User/Register" method="POST" enctype="multipart/form-data">

    <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
    <h1 class="h3 mb-3 font-weight-normal">Register Account</h1>
    
    <label for="username" class="sr-only">Username</label>
    <input type="text" name="username" id="username" class="form-control" placeholder="Username" required autofocus pattern="[A-Za-z0-9]{3,20}" title="Username should be 3-20 characters long and contain only letters and numbers.">
    
    <label for="password" class="sr-only">Password</label>
    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required pattern=".{8,}" title="Password must be at least 8 characters long.">
    
    <label for="password_confirm" class="sr-only">Confirm Password</label>
    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirm Password" required pattern=".{8,}" title="Password must be at least 8 characters long.">
    
    <input type="hidden" name="csrf" id="csrf" value="<?= $CSRF ?>">

    <button class="btn btn-lg btn-primary btn-block" type="submit">Register New Account</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2023-2024</p>

    <?php if ($Error): ?>
            <div class='alert alert-warning' role='alert'>
                Error: <?= htmlspecialchars($Error, ENT_QUOTES, 'UTF-8') ?>
            </div>
    <?php endif; ?>
</form>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
