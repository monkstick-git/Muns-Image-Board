<?php

ob_start();
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card mb-4 box-shadow">
                <form class="form-short" action="<?= Registry::get("RouteTranslations")['ShortCreate']; ?>"
                    method="POST" enctype="multipart/form-data">
                    <h1 class="h3 mb-3 font-weight-normal">Create a Short URL</h1>
                    <label for="longUrl" class="sr-only">Long URL</label>
                    <input type="url" name="longUrl" id="longUrl" class="form-control" placeholder="Enter the long URL"
                        required autofocus>
                    <label for="customAlias" class="sr-only">Custom Alias (optional)</label>
                    <input type="text" name="customAlias" id="customAlias" class="form-control"
                        placeholder="Custom alias (optional)">
                    <input type="hidden" name="csrf" value="<?= $CSRF ?>">
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Shorten URL</button>
                    <p class="mt-5 mb-3 text-muted">&copy; Kieron 2024</p>

                    <?php if (isset($Error)): ?>
                        <div class="alert alert-warning" role="alert">
                            <?= $Error ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($Success)): ?>
                        <div class="alert alert-success" role="alert">
                            <?= $Success ?>
                        </div>
                    <?php endif; ?>

                </form>

            </div>
        </div>
    </div>
</div>

<?php
$template = ob_get_contents();
ob_end_clean();
?>