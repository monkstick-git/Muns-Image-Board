<?php
ob_start();
?>

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
      <div id="uploaded_image" class="row mt-4"></div>
    </div>
  </div>
</div>

<script>
  function _(element) {
    return document.getElementById(element);
  }

  _('upload_form').onsubmit = function (event) {
    event.preventDefault();  // Prevent the default form submission
    var form_data = new FormData();
    var error = '';

    for (var count = 0; count < _('select_file').files.length; count++) {
      form_data.append("filesToUpload[]", _('select_file').files[count]);
    }

    form_data.append("public", _('public').checked);

    if (error != '') {
      _('uploaded_image').innerHTML = error;
      _('select_file').value = '';
    } else {
      _('progress_bar').style.display = 'block';
      var ajax_request = new XMLHttpRequest();
      ajax_request.open("POST", "/Site/Upload");
      
      // Upload progress
      ajax_request.upload.addEventListener('progress', function (event) {
        var percent_completed = Math.round((event.loaded / event.total) * 100);
        _('progress_bar_process').style.width = percent_completed + '%';
        _('progress_bar_process').innerHTML = percent_completed + '% uploaded';
      });

      // On upload completion
      ajax_request.addEventListener('load', function (event) {
        if (ajax_request.status === 507) {  // Quota exceeded
          _('uploaded_image').innerHTML = '<div class="alert alert-danger">Quota reached. Upload failed.</div>';
        } else if (ajax_request.status === 200) {  // Success
          // Switch to processing phase
          _('progress_bar_process').innerHTML = 'Processing... Please wait';

          // Wait for a small delay to simulate processing time, then show success message
          setTimeout(function () {
            _('uploaded_image').innerHTML = '<div class="alert alert-success">Files Uploaded Successfully</div>';
            _('select_file').value = '';
            _('progress_bar').style.display = 'none';
            _('progress_bar_process').style.width = '0%';
            _('progress_bar_process').innerHTML = '0%';
          }, 2000); // Adjust the delay time as needed
        } else {  // Other errors
          _('uploaded_image').innerHTML = '<div class="alert alert-danger">An error occurred during upload. Please try again.</div>';
        }
      });

      ajax_request.addEventListener('error', function (event) {
        _('uploaded_image').innerHTML = '<div class="alert alert-danger">An error occurred during upload. Please try again.</div>';
        _('progress_bar').style.display = 'none';
        _('progress_bar_process').style.width = '0%';
        _('progress_bar_process').innerHTML = '0%';
      });

      ajax_request.send(form_data);
    }
  };
</script>



<?php
$template = ob_get_contents();
ob_end_clean();
?>