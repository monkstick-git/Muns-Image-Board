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
      'My Files' => '/User/myfiles',
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
#echo "<pre> " . print_r($_SESSION,true) . "</pre>";
ob_start();

?>

<header>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">MunBoard</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
        aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <ul class="navbar-nav mr-auto">
        <?php foreach ($MenuItems['NavBar'] as $key => $value): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $value; ?>"><?php echo $key; ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
      <ul class="navbar-nav ml-auto">
        <?php if ((isset($_SESSION['logged_in']) != true)): ?>
          <?php foreach ($MenuItems['RightBarLoggedOut'] as $key => $value): ?>
            <?php if (gettype($value) == 'array'): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">Logged Out</a>
                <il class="dropdown-menu">
                  <?php foreach ($value as $key => $value): ?>
                    <a class='dropdown-item' href='<?php echo $value; ?>'><?php echo $key; ?></a>
                  <?php endforeach; ?>
                </il>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <?php foreach ($MenuItems['RightBarLoggedIn'] as $key => $value): ?>
            <?php if (gettype($value) == 'array'): ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false"><?php echo $GLOBALS['User']->username; ?></a>
                <div class="dropdown-menu" aria-labelledby="dropdown08">
                  <?php foreach ($value as $key => $value): ?>
                    <a class='dropdown-item' href='<?php echo $value; ?>'><?php echo $key; ?></a>
                  <?php endforeach; ?>
                </div>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</header>
<main role="main" class="main">

<?php
$template = ob_get_contents();
ob_end_clean();
?>