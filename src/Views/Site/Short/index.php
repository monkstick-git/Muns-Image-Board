<?php 
$Success = $arguments['shortURL'] ?? false;
ob_start(); ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card mb-4 box-shadow p-4">
                <form class="form-short" action="<?= Registry::get("RouteTranslations")['ShortCreate']; ?>"
                    method="POST" enctype="multipart/form-data">
                    
                    <!-- Form Title -->
                    <h1 class="h3 mb-3 font-weight-normal text-center">Create a Short URL</h1>
                    
                    <!-- Long URL Input -->
                    <div class="mb-3">
                        <label for="longUrl" class="form-label">Long URL</label>
                        <input type="url" name="longUrl" id="longUrl" class="form-control"
                            placeholder="Enter the long URL" required autofocus>
                        <small class="form-text text-muted">Enter the full URL you want to shorten.</small>
                    </div>

                    <!-- A Bootstrap line break -->
                    <hr class="my-4">
                    <!-- Submit Button -->
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Shorten URL</button>
                    <a href="<?= Registry::get("RouteTranslations")['ShortList']; ?>" class="btn btn-lg btn-secondary btn-block">Your Short URLs</a>
                    <?php if ($Success): ?>
                        <div class="alert alert-success mt-3 text-center">Short URL created: <a href="/s?s=<?= $Success; ?>" target="_blank"><?= $Success; ?></a></div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $template = ob_get_contents(); ob_end_clean(); ?>
