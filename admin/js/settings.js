jQuery(document).ready(function ($) {
  $(".cordy-upload-btn").on("click", function (e) {
    e.preventDefault();

    var target = $(this).data("target");
    var button = $(this);

    var frame = wp.media({
      title: "Select Image",
      button: { text: "Use this image" },
      multiple: false,
      library: { type: "image" },
    });

    frame.on("select", function () {
      var attachment = frame.state().get("selection").first().toJSON();
      var url = attachment.url;

      $("#" + target).val(url);

      var preview = button.siblings(".cordy-image-preview");
      if (preview.length) {
        preview.find("img").attr("src", url);
      } else {
        button.after(
          '<div class="cordy-image-preview" style="margin-top:8px;">' +
            '<img src="' +
            url +
            '" style="max-width:400px;max-height:120px;display:block;border:1px solid #ddd;padding:4px;background:#fff;" />' +
            "</div>",
        );
      }
    });

    frame.open();
  });

  // Font upload — opens media library without image-type filter
  $(".cordy-upload-font-btn").on("click", function (e) {
    e.preventDefault();

    var target = $(this).data("target");
    var button = $(this);

    var frame = wp.media({
      title: "Upload Font File",
      button: { text: "Use this font" },
      multiple: false,
    });

    frame.on("select", function () {
      var attachment = frame.state().get("selection").first().toJSON();
      var url = attachment.url;
      var filename = attachment.filename || url.split("/").pop();

      $("#" + target).val(url);

      var hint = button.siblings(".description");
      if (hint.length) {
        hint.text(filename);
      } else {
        button.after(
          '<span class="description" style="margin-left:8px;">' +
            filename +
            "</span>",
        );
      }
    });

    frame.open();
  });
});
