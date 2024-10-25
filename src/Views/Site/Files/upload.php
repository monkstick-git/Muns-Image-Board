<?php ob_start(); ?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <h1 class="mt-4 mb-4 text-center">File Upload</h1>

      <div class="card">
        <div class="card-header text-center">Select File</div>
        <div class="card-body">
          <form id="upload_form">
            <div class="mb-3">
              <label for="select_file" class="form-label"><b>Select File</b></label>
              <input class="form-control" type="file" id="select_file" multiple />
            </div>
            <div class="mb-3 form-check">
              <input class="form-check-input" type="checkbox" id="public" name="public" value="1" />
              <label class="form-check-label" for="public">Make file public</label>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Upload</button>
            </div>
          </form>
        </div>
      </div>

      <div class="progress mt-4" id="progress_bar" style="display:none;">
        <div class="progress-bar" id="progress_bar_process" role="progressbar" style="width:0%">0%</div>
      </div>
      <div id="processing_message" class="mt-2" style="display:none;">
        <div class="alert alert-info text-center">
          Processing... This may take a few seconds, please do not close the window.
        </div>
      </div>
      <div id="uploaded_image" class="row mt-4"></div>
    </div>
  </div>
</div>

<script>
  function _(element) {
    return document.getElementById(element);
  }

  _('upload_form').onsubmit = function (event) {
    event.preventDefault();  // Prevent default form submission
    var form_data = new FormData();

    // Append selected files to FormData
    for (var count = 0; count < _('select_file').files.length; count++) {
      form_data.append("filesToUpload[]", _('select_file').files[count]);
    }
    form_data.append("public", _('public').checked);

    _('progress_bar').style.display = 'block';  // Show progress bar
    var ajax_request = new XMLHttpRequest();
    ajax_request.open("POST", "/File/ProcessUpload");

    // Upload progress event listener
    ajax_request.upload.addEventListener('progress', function (event) {
      var percent_completed = Math.round((event.loaded / event.total) * 100);
      _('progress_bar_process').style.width = percent_completed + '%';
      _('progress_bar_process').innerHTML = percent_completed + '% uploaded';

      // When upload reaches 100%, switch to "processing" state
      if (percent_completed === 100) {
        _('progress_bar_process').innerHTML = 'Upload complete. Processing...';
        _('processing_message').style.display = 'block';  // Show processing message
      }
    });

    // On upload completion
    ajax_request.addEventListener('load', function (event) {
      if (ajax_request.status === 507) {  // Quota exceeded
        _('uploaded_image').innerHTML = '<div class="alert alert-danger">Quota reached. Upload failed.</div>';
        resetProgressBar();
      } else if (ajax_request.status === 200) {  // Success
        // After a delay (to simulate processing), show success message
        setTimeout(function () {
          _('uploaded_image').innerHTML = '<div class="alert alert-success">Files Uploaded Successfully</div>';
          resetProgressBar();
        }, 2000);  // Adjust delay for processing simulation
      } else {  // Other errors
        _('uploaded_image').innerHTML = '<div class="alert alert-danger">An error occurred during upload. Please try again.</div>';
        resetProgressBar();
      }
    });

    // Error handling
    ajax_request.addEventListener('error', function (event) {
      _('uploaded_image').innerHTML = '<div class="alert alert-danger">An error occurred during upload. Please try again.</div>';
      resetProgressBar();
    });

    // Send the form data
    ajax_request.send(form_data);
  };

  // Function to reset the progress bar and hide processing message
  function resetProgressBar() {
    _('progress_bar').style.display = 'none';
    _('progress_bar_process').style.width = '0%';
    _('progress_bar_process').innerHTML = '0%';
    _('processing_message').style.display = 'none';
    _('select_file').value = '';  // Clear the file input
  }
</script>

<?php $template = ob_get_contents(); ob_end_clean(); ?>
