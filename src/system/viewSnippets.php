<?php

function insertCardTemplate($fileName, $fileID, $uploadDate, $htmlContent)
{
    echo '
    <div class="col-md-4 d-flex align-items-stretch">
        <div class="card mb-4 shadow-sm" style="width: 100%;">
            ' . $htmlContent . '
            <div class="card-body">
                <h5 class="card-title">' . htmlspecialchars($fileName) . '</h5>
                <h6 class="card-subtitle mb-2 text-muted">' . htmlspecialchars($uploadDate) . '</h6>
                <p class="card-text">Click the buttons below to view details or delete this file.</p>
                <div class="d-flex justify-content-between">
                    <a href="/Site/files/details?id=' . htmlspecialchars($fileID) . '" class="btn btn-primary">Details</a>
                    <a href="/Site/files/delete?id=' . htmlspecialchars($fileID) . '" class="btn btn-danger">❌ Delete ❌</a>
                </div>
            </div>
        </div>
    </div>';
}

function insertImageCard($fileURL, $thumbnailfileURL, $fileName, $fileID, $uploadDate)
{
    $htmlContent = '
    <a href="' . htmlspecialchars($fileURL) . '">
        <img class="card-img-top lazyload" data-src="' . htmlspecialchars($thumbnailfileURL) . '" alt="Thumbnail"
             src="/assets/Images/loading.gif" data-holder-rendered="true" lazyload="on">
    </a>';

    insertCardTemplate($fileName, $fileID, $uploadDate, $htmlContent);
}

function insertVideoCard($fileURL, $fileName, $fileID, $uploadDate)
{
    $htmlContent = '
            <video
                controls
                preload="auto"
                class="embed-responsive-item"
                width="100%"
                height="auto">
                <source src="'.htmlspecialchars($fileURL).' data-holder-rendered="true" lazyload="on"">
                Your browser does not support the video tag.
            </video>
    ';

    insertCardTemplate($fileName, $fileID, $uploadDate, $htmlContent);
}

?>
