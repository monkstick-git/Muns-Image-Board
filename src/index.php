<?php include './system/bootstrap.php'; ?>
<?php $render->render_template('navbar');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
  header('Location: /User/login');
}
else {
  header('Location: /Site/Gallary');
}

?>

