<?php
$Username = $_SESSION['user']->username;
$hashed_email = md5($_SESSION['user']->email);
$gravatar = "https://www.gravatar.com/avatar/$hashed_email?s=200";
$FirstName = $_SESSION['user']->name;
$LastName = $_SESSION['user']->surname;
$MemberSince = $_SESSION['user']->Datecreated;
$Space = floor($_SESSION['user']->getSpaceUsed() / 1024 / 1024);
$Quota = floor($_SESSION['user']->quota);
$bio = $_SESSION['user']->bio;
$ImageCount = $_SESSION['user']->getImageCount();
$apiKey = $_SESSION['user']->apiKey;

ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <div class="text-center">
                    <img src="<?= $gravatar ?>" class="rounded-circle" height="100" width="100" alt="User Avatar">
                    <h3 class="mt-3"><?= $FirstName ?> <?= $LastName ?></h3>
                    <p class="text-muted text-center">@<?= $Username ?></p>  <!-- Added text-center class here -->
                    <p><strong>Uploads:</strong> <?= $ImageCount ?></p>
                    <p><strong>Space Used:</strong> <?= $Space ?>/<?= $Quota ?> MB</p>
                    <a href="/User/edit" class="btn btn-dark mt-2">Edit Profile</a>
                    <p class="mt-3"><?= $bio ?></p>
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#apiField" aria-expanded="false">Toggle API Key</button>
                    <button class="btn btn-secondary" onclick="copyToClipboard('<?= $apiKey ?>')">📋 Copy API Key</button>
                </div>
                <div class="collapse mt-3" id="apiField">
                    <div class="alert alert-info">
                        <pre><?= $apiKey ?></pre>
                    </div>
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


<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => {
                alert('API Key copied to clipboard!');
            })
            .catch((error) => {
                console.error('Failed to copy API Key:', error);
            });
    }
</script>

<?php
$template = ob_get_contents();
ob_end_clean();
