<?php

$MenuItems = array(
  'NavBar' => array(
    'Home' => '/',
    'Upload' => '/Site/Upload',
    'Gallary' => '/Site/Gallary'
  ),
  'RightBarLoggedIn' => array(
    'account' => array(
      'Profile' => '/User/profile',
      'Logout' => '/User/logout'
    )
    ),
    'RightBarLoggedOut' => array(
      'account' => array(
        'Register' => '/User/register',
        'Login' => '/User/login'
      )
    )    
);


$template = '
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <a class="navbar-brand" href="#">MunBoard</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

        <ul class="navbar-nav mr-auto">
';

foreach ($MenuItems['NavBar'] as $key => $value) {
  $template .= '
          <li class="nav-item">
            <a class="nav-link" href="' . $value . '">' . $key . '</a>
          </li>
  ';
}

$template .= '
        </ul>
        <ul class="navbar-nav ml-auto">
';

if ((isset($_SESSION['logged_in']) != true)) {
  # If not logged in
  foreach ($MenuItems['RightBarLoggedOut'] as $key => $value) {
    if (gettype($value) == 'array') {
      $template .= '
  
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Logged Out</a>
      <div class="dropdown-menu" aria-labelledby="dropdown08">
      ';
      foreach ($value as $key => $value) {
        $template .= "
        <a class='dropdown-item' href='$value'>$key</a>
        ";
      }
      $template .= '</div></li>';
    }
  }
}else{
  foreach ($MenuItems['RightBarLoggedIn'] as $key => $value) {
    if (gettype($value) == 'array') {
      $template .= '
  
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $GLOBALS['User']->username . '</a>
      <div class="dropdown-menu" aria-labelledby="dropdown08">
      ';
      foreach ($value as $key => $value) {
        $template .= "
        <a class='dropdown-item' href='$value'>$key</a>
        ";
      }
      $template .= '</div></li>';
    }
  }
}



$template .= '
</div>
</li>
</ul>
</div>
</nav>
';

