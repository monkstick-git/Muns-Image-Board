<?php
$Username = $_SESSION['user']->username;
$hashed_email = md5($_SESSION['user']->email);
$gravitar = "http://www.gravatar.com/avatar/$hashed_email?s=512";
$Username = $_SESSION['user']->username;
$FirstName = $_SESSION['user']->name;
$LastName = $_SESSION['user']->surname;
$MemberSince = $_SESSION['user']->Datecreated;
$Space = floor($_SESSION['user']->getSpaceUsed() / 1024 / 1024);
$bio = $_SESSION['user']->bio;
$ImageCount = $_SESSION['user']->getImageCount();

$Error = $arguments['errors'] ?? null;
$NoMatch = $arguments['errors']['nomatch'] ?? null;
$PasswordSuccess = $arguments['success']['password'] ?? null;


ob_start();
?>

<div class='container'>
  <div class='row'>
    <div class=' col-sm'></div>
    <div class='container'>
      <div class='row'>
        <div class='col-sm'></div>
        <div class='container mt-4 mb-4 p-3 d-flex justify-content-center'>
          <div class='card p-4'>
            <div class='image d-flex flex-column justify-content-center align-items-center'>
              <button class='btn btn-secondary'>
                <a href=<?= $gravitar ?>> <img src="<?= $gravitar ?>" height='100' width='100' /> </a>
              </button>
              <span class='name mt-3'><?= $FirstName . " " . $LastName ?></span>
              <span class='idd'><?= $Username ?></span>
              <div class='d-flex flex-row justify-content-center align-items-center gap-2'>
                <span class='idd1'>Uploads: <?= $ImageCount ?></span>
                <span><i class='fa fa-copy'></i></span>
              </div>
              <div class='d-flex flex-row justify-content-center align-items-center mt-3'>
                <span class='number'><?= $Space ?> MB <span class='follow'>Space Used</span></span>
              </div>
              <div class='d-flex mt-2'>
                <form action='/User/edit' method='post'>
                  <?php
                  if ($NoMatch) {
                    echo ("<div class='alert alert-warning' role='alert'>");
                    echo ($NoMatch);
                    echo ("</div><br>");
                  }
                  if ($PasswordSuccess) {
                    echo ("<div class='alert alert-success' role='alert'>");
                    echo ($PasswordSuccess);
                    echo ("</div><br>");
                  }
                  ?>
                  <label for='password'>New Password:</label>
                  <input type='password' id='password' name='password'><br>
                  <label for='passwordConfirm'>Confirm New Password:</label>
                  <input type='password' id='passwordConfirm' name='passwordConfirm'><br>
                  <input type='submit' value='Submit'>
                </form>
              </div>
              <div class='text mt-3'>
                <span><?= $bio ?>
                </span>
              </div>
              <div class='gap-3 mt-3 icons d-flex flex-row justify-content-center align-items-center'>
                <span><i class='fa fa-twitter'></i></span>
                <span><i class='fa fa-facebook-f'></i></span>
                <span><i class='fa fa-instagram'></i></span>
                <span><i class='fa fa-linkedin'></i></span>
              </div>
              <div class='px-2 rounded mt-4 date'>
                <span class='join'>Joined <?= $MemberSince ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class='col-sm'></div>
      </div>
    </div>
    <div class=' col-sm'></div>

  </div>
</div>

<?php
$template = ob_get_contents();
ob_end_clean();