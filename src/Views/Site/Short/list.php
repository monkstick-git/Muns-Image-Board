<?php
$shortURLs = $arguments['shortURLs'] ?? [];
ob_start();
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <!-- Start of the card container -->
            <div class="card mb-4 box-shadow p-4">

                <!-- Header Section: Title and Create New Button -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- Title for the page -->
                    <h1 class="h3 mb-3 font-weight-normal">Your Short URLs</h1>
                    
                    <!-- Button to create a new short URL -->
                    <a href="<?= Registry::get("RouteTranslations")['ShortNew'] ?>" class="btn btn-primary">Create New Short URL</a>
                </div>

                <!-- Conditional Section: Check if there are any short URLs -->
                <?php if (empty($shortURLs)): ?>
                    <!-- If no short URLs, display this info alert -->
                    <div class="alert alert-info text-center">No short URLs found</div>
                <?php else: ?>
                    
                    <!-- List Group Section: Display each short URL in a card-like style -->
                    <div class="list-group">
                        <?php foreach ($shortURLs as $shortURL): ?>
                            <!-- Each short URL is displayed as a list group item -->
                            <div class="list-group-item list-group-item-action mb-3">
                                
                                <!-- Top row with the short URL and creation date -->
                                <div class="d-flex w-100 justify-content-between">
                                    <!-- The short URL as a clickable link -->
                                    <h5 class="mb-1">
                                        <a href="/s?s=<?= htmlspecialchars($shortURL['short_url'], ENT_QUOTES) ?>" target="_blank">
                                            <?= htmlspecialchars($shortURL['short_url'], ENT_QUOTES) ?>
                                        </a>
                                    </h5>
                                    
                                    <!-- Display the creation date -->
                                    <small>
                                        <?= htmlspecialchars($shortURL['created'], ENT_QUOTES) ?>
                                    </small>
                                </div>
                                
                                <!-- The long URL is shown as the description text -->
                                <p class="mb-1"><?= htmlspecialchars($shortURL['long_url'], ENT_QUOTES) ?></p>

                                <!-- Delete Button Section -->
                                <div class="text-end">
                                    <button class="btn btn-sm btn-danger" onclick="showDeleteModal('<?= $shortURL['id']; ?>')">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- End of List Group Section -->
                <?php endif; ?>

            </div>
            <!-- End of the card container -->
        </div>
    </div>
</div>

<!-- Bootstrap Modal for delete confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Short URL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this short URL? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for handling the delete functionality with modal -->
<script>
    let deleteId = null;

    // Show the modal and store the ID of the URL to be deleted
    function showDeleteModal(id) {
        deleteId = id;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'), {});
        deleteModal.show();
    }

    // Handle the delete action when the delete button in the modal is clicked
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteId !== null) {
        // Send AJAX request to delete the short URL via POST
        fetch(`/S/delete?id=${deleteId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
        })
        .then(response => {
            if (response.ok) { // Check if the response status is 200-299
                // If deletion is successful, refresh the page
                location.reload(); // Reload the page to update the list
            } else {
                // Handle failure case
                alert('Failed to delete the short URL. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
});

</script>

<?php
$template = ob_get_contents();
ob_end_clean();
?>
