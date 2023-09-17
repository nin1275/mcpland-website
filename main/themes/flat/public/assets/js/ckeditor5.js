var ckeditor;
var ckeditorEl = document.getElementById('ckeditor');
if (ckeditorEl) {
  var csrfToken = ckeditorEl.dataset.csrf;
  var uploadUrl = ckeditorEl.dataset.upload;
  ckeditorEl.style.display = 'block';
  ClassicEditor
    .create(ckeditorEl, {
      language: 'en',
      toolbar: {
        items: [
          "heading",
          "bold",
          "italic",
          "strikethrough",
          "link",
          "bulletedList",
          "imageUpload",
          "codeBlock",
          "|",
          "undo",
          "redo"
        ]
      },
      codeBlock: {
        languages: [
          {language: 'html', label: 'HTML'},
          {language: 'css', label: 'CSS'},
          {language: 'js', label: 'JavaScript'},
          {language: 'yaml', label: 'YAML'},
        ]
      },
      simpleUpload: {
        // The URL that the images are uploaded to.
        uploadUrl: uploadUrl,
        
        // Enable the XMLHttpRequest.withCredentials property.
        withCredentials: true,
        
        // Headers sent along with the XMLHttpRequest to the upload server.
        headers: {
          'X-CSRF-TOKEN': csrfToken,
        }
      }
    })
    .then(newEditor => {
      ckeditor = newEditor;
    })
    .catch(error => {
      console.error(error);
    });
}