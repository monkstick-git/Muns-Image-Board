<?php
$FileArray = $arguments['FileArray'];
$adminMenu = $arguments['adminMenu'];
ob_start();
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let isFetching = false;
    const galleryContainer = document.querySelector('.gallery-container');
    let totalPages = parseInt(galleryContainer.dataset.totalPages);
    let currentPage = parseInt(galleryContainer.dataset.currentPage);

    window.addEventListener('scroll', () => {
        if (isFetching) return;

        let offsetPercent = 30;
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - (document.body.offsetHeight * (offsetPercent / 100))) {
            fetchMoreImages();
        }
    });

    function fetchMoreImages() {
        if (currentPage >= totalPages) return; // Stop fetching if last page is reached
        isFetching = true;
        currentPage++;
        galleryContainer.dataset.currentPage = currentPage; // Update the data attribute

        fetch(`/Home/Gallery?page=${currentPage}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Append only new gallery items
                tempDiv.querySelectorAll('.col-md-4').forEach(item => {
                    galleryContainer.appendChild(item);
                });
            })
            .catch(error => console.error('Error fetching images:', error))
            .finally(() => {
                isFetching = false; // Allow new fetches once this one completes
            });
    }
});
</script>



<div class="container-fluid album py-5 bg-light">
    <div class="row justify-content-center">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-6 g-3 gallery-container" data-total-pages="<?= $arguments['totalPages'] ?>" data-current-page="1">
            <?php foreach ($FileArray as $key => $value): ?>
                <?php
                $fileValue = $value;
                $fileID = $fileValue['hash'] . "_" . $fileValue['id'];
                if ($adminMenu):
                    $fileOwner = $fileValue['owner'];
                    $tmpUser = new user(); # TODO: This should be passed to the view, not created here
                    $tmpUser->get_user_by_id($fileOwner);
                    $ownerName = $tmpUser->username;
                endif;
                $fileType = explode("/", ($fileValue['filetype']))[1];
                $fileContentType = explode("/", ($fileValue['filetype']))[0]; # Image, Video etc
            
                $thumbnailfileURL = "/image/thumbnail?id=$fileID.$fileType";
                $fileURL = "/image/raw?id=$fileID.$fileType";
                $file = new image(); # TODO: This should be passed to the view, not created here
                $file->getFileMetadata($fileID);
                $fileName = $file->FileName;
                $modified = $file->Modified;
                if ($adminMenu):
                    $adminString = "<div>$ownerName</div>";
                else:
                    $adminString = "";
                endif;
                ?>

                <?php
                if ($fileContentType == "image"):
                    insertImageCard($fileURL, $thumbnailfileURL, $fileName, $fileID, $modified);
                endif;

                if ($fileContentType == "video"):
                    insertVideoCard($fileURL, $fileName, $fileID, $modified);
                endif;
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<?php
$template = ob_get_contents();
ob_end_clean();
?>