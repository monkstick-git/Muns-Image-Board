<?php

/**
 * Logs a message to the PHP error log with a custom prefix.
 *
 * @param string $str The message to log.
 */
function logger($str)
{
    error_log("MunBoard Log: \n" . $str . "\n");
}

/**
 * Reorganizes the $_FILES array structure to be more manageable.
 *
 * This function takes the array structure created by PHP for file uploads
 * (when multiple files are uploaded) and reorganizes it into a more manageable format.
 *
 * @param array $file_post The original $_FILES array.
 * @return array The reorganized array of files.
 */
function reArrayFiles(&$file_post)
{
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

/**
 * Decompresses and decodes a base64-encoded and compressed string.
 *
 * @param string $data The base64-encoded and compressed data.
 * @return string The decompressed data.
 */
function Expand($data)
{
    $data = base64_decode($data);
    $data = gzuncompress($data);
    return $data;
}

/**
 * Compresses and encodes a string using gzcompress and base64 encoding.
 *
 * @param string $data The data to compress and encode.
 * @return string The compressed and base64-encoded string.
 */
function Compress($data, $level = 1, $encoding = "base64")
{
    $data = gzcompress($data, 1);
    $data = base64_encode($data);
    return $data;
}

function getClientRealIP(){
    // Get the client's real IP address, accounting for proxies and Cloudflare
$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

if (isset($_SERVER['HTTP_X_REAL_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
}

if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
}

if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

Registry::set('ClientIP', $_SERVER['REMOTE_ADDR']);
}


/////// I want to move these snippets somewhere else, but I'm not sure where as of now. ///////
///////////////////////////////////////////////////////////////////////////////////////////////
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
                <source src="' . htmlspecialchars($fileURL) . ' data-holder-rendered="true" lazyload="on"">
                Your browser does not support the video tag.
            </video>
    ';

    insertCardTemplate($fileName, $fileID, $uploadDate, $htmlContent);
}
///////////////////////////////////////////////////////////////////////////////////////////////