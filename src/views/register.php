<?php
$Error = false;

if (isset($arguments)) {
  if (isset($arguments['errors'])) {
    $Error = $arguments['errors'];
  }
}

$template = '
    <link href="/assets/css/signin.css" rel="stylesheet">
    <form class="form-signin" action="register" method="POST" enctype="multipart/form-data">
      <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">Register Account</h1>
      <label for="username" class="sr-only">Username</label>
      <input type="text" name="username" id="username" class="form-control" placeholder="Username" required autofocus>
      <label for="password" class="sr-only">Password</label>
      <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
      <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirm Password" required>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      <p class="mt-5 mb-3 text-muted">&copy; 2017-2018</p>
';

if($Error){
  foreach ($Error as $key => $value) {
    $Extra = "
    <div class='alert alert-warning' role='alert'>
      Error: $value 
    </div>
    ";  
  }
  

  $template .= $Extra;
}

$template .= "</form>";
