<?php

/**
 * Inserts a card template with the given file details and content.
 *
 * @param string $fileName The name of the file.
 * @param string $fileID The unique ID of the file.
 * @param string $uploadDate The date the file was uploaded.
 * @param string $htmlContent The HTML content to be inserted inside the card.
 *
 * @return void
 */
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
                    <a href="/File/Details?id=' . htmlspecialchars($fileID) . '" class="btn btn-primary">Details</a>
                    <a href="/File/Delete?id=' . htmlspecialchars($fileID) . '" class="btn btn-danger">❌ Delete ❌</a>
                </div>
            </div>
        </div>
    </div>';
}

/**
 * Inserts an image card with a file thumbnail and link to the file.
 *
 * @param string $fileURL The URL of the full-sized image file.
 * @param string $thumbnailfileURL The URL of the image thumbnail.
 * @param string $fileName The name of the image file.
 * @param string $fileID The unique ID of the file.
 * @param string $uploadDate The date the file was uploaded.
 *
 * @return void
 */
function insertImageCard($fileURL, $thumbnailfileURL, $fileName, $fileID, $uploadDate)
{
    $htmlContent = '
    <a href="' . htmlspecialchars($fileURL) . '">
        <img class="card-img-top lazyload" 
             src="/assets/Images/loading.webp" 
             data-src="' . htmlspecialchars($thumbnailfileURL) . '" 
             alt="Thumbnail" 
             loading="lazy">
    </a>';
    

    insertCardTemplate($fileName, $fileID, $uploadDate, $htmlContent);
}

/**
 * Inserts a video card with a video player.
 *
 * @param string $fileURL The URL of the video file.
 * @param string $fileName The name of the video file.
 * @param string $fileID The unique ID of the file.
 * @param string $uploadDate The date the file was uploaded.
 *
 * @return void
 */
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
