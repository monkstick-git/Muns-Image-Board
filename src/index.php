<?php include './system/bootstrap.php'; ?>
<?php $render->render_template('navbar');

if (!$_SESSION['logged_in']) {
  header('Location: /User/login');
}
else {
  header('Location: /Site/Gallary');
}

?>

