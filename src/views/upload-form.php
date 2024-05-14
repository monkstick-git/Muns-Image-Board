<?php
ob_start();
?>

<div class="container">
  <div class="row justify-content-start">
    <div class="col-10">
      <h1 class="mt-3 mb-3 text-center">File Upload</h1>

      <div class="card">
        <div class="card-header">Select File</div>
        <div class="card-body">
          <table class="table">
            <tr>
              <td width="50%" align="right"><b>Select File</b></td>
              <td width="50%">
                <input type="file" id="select_file" multiple />
                <!-- Add a tickbox for 'Public' or 'Private' -->
                <br>
                <label for="public">Should File be Public</label>
                <input type="checkbox" id="public" name="public" value="1" />
              </td>
            </tr>
          </table>
        </div>
      </div>
      <br />

      <div class="progress" id="progress_bar" style="display:none; ">
        <div class="progress-bar" id="progress_bar_process" role="progressbar" style="width:0%">0%</div>
      </div>
      <div id="uploaded_image" class="row mt-5"></div>
    </div>

    <script>
      function _(element) {
        return document.getElementById(element);
      }

      _('select_file').onchange = function (event) {
        var form_data = new FormData();
        var image_number = 1;
        var error = '';

        for (var count = 0; count < _('select_file').files.length; count++) {
          form_data.append("filesToUpload[]", _('select_file').files[count]);
          // add the public checkbox value to the form data
          form_data.append("public", _('public').checked);
          console.log(_('public').checked);
          image_number++;
        }

        if (error != '') {
          _('uploaded_image').innerHTML = error;
          _('select_file').value = '';
        }
        else {
          _('progress_bar').style.display = 'block';
          var ajax_request = new XMLHttpRequest();
          ajax_request.open("POST", "/Site/Upload");
          ajax_request.upload.addEventListener('progress', function (event) {
            var percent_completed = Math.round((event.loaded / event.total) * 100);
            _('progress_bar_process').style.width = percent_completed + '%';
            _('progress_bar_process').innerHTML = percent_completed + '% completed';
          });

          ajax_request.addEventListener('load', function (event) {
            _('uploaded_image').innerHTML = '<div class="alert alert-success">Files Uploaded Successfully</div>';
            _('select_file').value = '';
          });
          ajax_request.send(form_data);
        }

      };

    </script>

  </div>

  <?php
  $template = ob_get_contents();
  ob_end_clean();
