<?php
$image = $arguments['file'];

$type = $image->FileType;
$shortType = explode("/", $type)[1];
$FileHash = $image->FileHash;
$FileID = $FileHash . "_" . $image->FileID;
$fileName = $image->FileName;
$modified = $image->Modified;
$fileType = explode("/", $type)[0]; // Getting the type before the slash, e.g., 'image' or 'video'

ob_start();
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card mb-4 box-shadow">
                <?php if ($fileType === 'image'): ?>
                    <a href="/images/raw/<?= $FileID ?>.<?= $shortType ?>">
                        <img class="card-img-top lazyload" 
                             data-src="/images/raw/<?= $FileID ?>.<?= $shortType ?>" 
                             alt="<?= $fileName ?>" 
                             src="https://cdn.dribbble.com/users/3251/screenshots/470914/aah.gif" 
                             data-holder-rendered="true" 
                             lazyload="on" 
                             style="width: 100%; height: auto;">
                    </a>
                <?php elseif ($fileType === 'video'): ?>
                    <div class="embed-responsive embed-responsive-16by9">
                        <video class="card-img-top embed-responsive-item" controls>
                            <source src="/images/raw/<?= $FileID ?>.<?= $shortType ?>" type="<?= $type ?>">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <p class="card-text text-center"><?= htmlspecialchars($fileName) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group">
                            <a type="button" class="btn btn-sm btn-outline-secondary" href="/files/raw/<?= $FileID ?>.<?= $shortType ?>">Download</a>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Delete</button>
                        </div>
                        <small class="text-muted"><?= htmlspecialchars($modified) ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.js"></script>
<script>
 $('.lazyload').lazyload();
</script>
<?php
$template = ob_get_contents();
ob_end_clean();
?>
