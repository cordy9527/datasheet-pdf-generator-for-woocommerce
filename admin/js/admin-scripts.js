jQuery(document).ready(function ($) {
  var editorId = "cordy_pdf_content";

  function getEditorContent() {
    if (window.tinyMCE && window.tinyMCE.get(editorId)) {
      return window.tinyMCE.get(editorId).getContent();
    }
    return $("#" + editorId).val();
  }

  function setEditorContent(content) {
    if (window.tinyMCE && window.tinyMCE.get(editorId)) {
      window.tinyMCE.get(editorId).setContent(content);
    } else {
      $("#" + editorId).val(content);
    }
  }

  // Import from the main WooCommerce product description editor
  $("#cordy-import-btn").on("click", function () {
    var productContent = "";
    if (window.tinyMCE && window.tinyMCE.get("content")) {
      productContent = window.tinyMCE.get("content").getContent();
    } else {
      productContent = $("#content").val();
    }
    if (!productContent) {
      alert("No product description found to import.");
      return;
    }
    setEditorContent(productContent);
  });

  $("#cordy-preview-btn").on("click", function (e) {
    e.preventDefault();

    var content = getEditorContent();
    if (!content) {
      alert("Please enter some PDF content first.");
      return;
    }

    $.ajax({
      url: cordyPdf.ajaxurl,
      type: "POST",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: {
        action: "cordy_preview_pdf",
        nonce: cordyPdf.nonce,
        pdfTitle: $("#title").val(),
        pdfContent: content,
        post_ID: $("#post_ID").val(),
      },
      success: function (response) {
        if (response.success) {
          var pdfWindow = window.open("", "_blank");
          if (pdfWindow) {
            pdfWindow.document.write(
              "<iframe width='100%' height='100%' src='data:application/pdf;base64," +
                encodeURI(response.data.pdf) +
                "'></iframe>",
            );
          } else {
            alert(
              "Unable to open PDF preview — please allow popups for this site.",
            );
          }
        } else {
          alert("Error: " + response.data);
        }
      },
      error: function (jqXHR, textStatus) {
        alert("AJAX error: " + textStatus);
      },
    });
  });

  $("#cordy-generate-btn").on("click", function () {
    var content = getEditorContent();
    if (!content) {
      alert("Please enter some PDF content first.");
      return;
    }

    if (!confirm("Generate and save a new PDF for this product?")) {
      return;
    }

    $.ajax({
      url: cordyPdf.ajaxurl,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: {
        action: "cordy_generate_pdf",
        nonce: cordyPdf.nonce,
        pdfTitle: $("#title").val(),
        pdfContent: content,
        post_ID: $("#post_ID").val(),
      },
      success: function (response) {
        if (response.success) {
          // Refresh the current PDF status link without a full page reload
          var statusEl = $(".cordy-pdf-status");
          var msg =
            "PDF generated: " +
            response.data.filename +
            " (" +
            response.data.filesize +
            ")";
          if (statusEl.length) {
            statusEl.html(
              'Current PDF: <a href="' +
                response.data.url +
                '" target="_blank">' +
                response.data.filename +
                "</a>",
            );
          } else {
            $(".cordy-pdf-buttons").after(
              '<p class="cordy-pdf-status">Current PDF: <a href="' +
                response.data.url +
                '" target="_blank">' +
                response.data.filename +
                "</a></p>",
            );
          }
          alert(msg);
        } else {
          alert("Error: " + response.data);
        }
      },
      error: function (jqXHR, textStatus) {
        alert("AJAX error: " + textStatus);
      },
    });
  });
});
