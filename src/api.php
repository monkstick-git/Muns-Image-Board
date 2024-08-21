<?php
include './system/bootstrap.php';
ob_clean();

// API for uploading images
// This API allows users to upload images and receive a link back to the uploaded image.
// The API is protected by an API key that is passed in the request for authentication.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['data'])) {
        respondWithError(400, "No file data provided.");
    }

    if (!isset($_REQUEST['api'])) {
        respondWithError(401, "API key is missing.");
    }

    // Authenticate the user using the API key
    $GLOBALS['User']->get_user_by_api($_REQUEST['api']);
    if (!$GLOBALS['User']->id) {
        respondWithError(401, "Invalid API key.");
    }

    // Process the uploaded file
    $file = new file();
    $file->setFromUpload($_FILES['data']);
    $fileType = explode("/", $file->FileType)[0];

    // Ensure only images are uploaded (for now)
    if ($fileType === "image") {
        $image = new image();
        $image->setFromUpload($_FILES['data']);
        $image->thumbnail = $image->CreateImageThumbNail();
        $image->PublicFile = 1; // Assume the image should be public
        $image->set();

        $type = explode("/", $image->FileType)[1];
        respondWithSuccess($settings['site_url'] . "images/raw/" . $image->FileHash . "_" . $image->FileID . "." . $type . "?api=1", $image->FileType);
    } else {
        // Handle non-image files
        $exploded = explode(".", $_FILES['data']['name']);
        $fileTypeGuess = end($exploded); // Guess the file type based on extension
        $file->set();
        
        respondWithSuccess($settings['site_url'] . "Site/download/" . $file->FileHash . "_" . $file->FileID . "." . $fileTypeGuess . "?api=1", $file->FileType);
    }
} else {
    respondWithError(405, "Method Not Allowed");
}

// Function to respond with a success message
function respondWithSuccess($url, $contentType) {
    ob_clean();
    ob_start();
    echo $url;
    header("Content-Type: $contentType");
    ob_end_flush();
    die;
}

// Function to respond with an error message and HTTP status code
function respondWithError($statusCode, $message) {
    header("HTTP/1.0 $statusCode $message");
    echo json_encode(['error' => $message]);
    die;
}
?>
