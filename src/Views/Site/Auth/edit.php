<?php
$Username = $_SESSION['user']->username;
$hashed_email = md5($_SESSION['user']->email);
$gravatar = "https://www.gravatar.com/avatar/$hashed_email?s=512";
$FirstName = $_SESSION['user']->name;
$LastName = $_SESSION['user']->surname;
$MemberSince = $_SESSION['user']->Datecreated;
$Space = floor($_SESSION['user']->getSpaceUsed() / 1024 / 1024);
$bio = $_SESSION['user']->bio;
$ImageCount = $_SESSION['user']->getImageCount();

$Error = $arguments['errors'] ?? null;
$NoMatch = $arguments['errors']['nomatch'] ?? null;
$PasswordSuccess = $arguments['success']['password'] ?? null;
$ProfileError = $arguments['errors']['profile'] ?? null;
$ProfileSuccess = $arguments['success']['profile'] ?? null;
ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <div class="text-center">
                    <a href="<?= $gravatar ?>"><img src="<?= $gravatar ?>" class="rounded-circle" height="100" width="100" alt="User Avatar"></a>
                    <h3 class="mt-3"><?= $FirstName . " " . $LastName ?></h3>
                    <p class="text-muted">@<?= $Username ?></p>
                    <p><strong>Uploads:</strong> <?= $ImageCount ?></p>
                    <p><strong>Space Used:</strong> <?= $Space ?> MB</p>
                </div>
                <div class="mt-3">
                    <form action="<?=Registry::get("RouteTranslations")['UserEditPage'];?>" method="post">
                        <?php if ($NoMatch): ?>
                            <div class="alert alert-warning" role="alert">
                                <?= $NoMatch ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($PasswordSuccess): ?>
                            <div class="alert alert-success" role="alert">
                                <?= $PasswordSuccess ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($ProfileError): ?>
                            <div class="alert alert-warning" role="alert">
                                <?= $ProfileError ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($ProfileSuccess): ?>
                            <div class="alert alert-success" role="alert">
                                <?= $ProfileSuccess ?>
                            </div>
                        <?php endif; ?>

                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name:</label>
                            <input type="text" id="firstName" name="firstName" class="form-control" value="<?= htmlspecialchars($FirstName) ?>">
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name:</label>
                            <input type="text" id="lastName" name="lastName" class="form-control" value="<?= htmlspecialchars($LastName) ?>">
                        </div>

                        <!-- Bio -->
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio:</label>
                            <textarea id="bio" name="bio" class="form-control" rows="3"><?= htmlspecialchars($bio) ?></textarea>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password:</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="passwordConfirm" class="form-label">Confirm New Password:</label>
                            <input type="password" id="passwordConfirm" name="passwordConfirm" class="form-control">
                        </div>

                        <div class="text-center">
                            <input type="submit" value="Submit" class="btn btn-primary">
                        </div>
                    </form>
                </div>
                <div class="text-center mt-3">
                    <a href="#"><i class="fa fa-twitter fa-lg mx-2"></i></a>
                    <a href="#"><i class="fa fa-facebook-f fa-lg mx-2"></i></a>
                    <a href="#"><i class="fa fa-instagram fa-lg mx-2"></i></a>
                    <a href="#"><i class="fa fa-linkedin fa-lg mx-2"></i></a>
                </div>
                <div class="text-center mt-4 text-muted">
                    <p>Joined <?= $MemberSince ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
