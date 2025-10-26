(function ($) {
  $(function () {
    var $container = $('#hram-home-slider-slides');
    var $input = $('#hram-home-slider-input');
    var frame = null;

    if (!$container.length || !$input.length) {
      return;
    }

    function updateInput() {
      var ids = [];

      $container.find('.hram-home-slider-slide').each(function () {
        var id = $(this).data('attachment-id');

        if (id) {
          ids.push(id);
        }
      });

      $input.val(ids.join(','));
    }

    function addAttachment(attachment) {
      var id = attachment.id;

      if (!id || $container.find('[data-attachment-id="' + id + '"]').length) {
        return;
      }

      var url = '';
      var alt = attachment.alt || attachment.title || '';

      if (attachment.sizes && attachment.sizes.thumbnail) {
        url = attachment.sizes.thumbnail.url;
      } else if (attachment.sizes && attachment.sizes.medium) {
        url = attachment.sizes.medium.url;
      } else {
        url = attachment.url;
      }

      var $slide = $('<div>', {
        'class': 'hram-home-slider-slide',
        'data-attachment-id': id
      });

      var $thumbWrapper = $('<div>', {
        'class': 'hram-home-slider-slide__thumbnail'
      });

      var $image = $('<img>', {
        src: url,
        alt: alt
      });

      var $remove = $('<button>', {
        type: 'button',
        'class': 'button-link-delete hram-home-slider-slide__remove',
        'aria-label': hramHomeSliderAdmin.i18n.removeSlide
      });

      $remove.append($('<span>', { 'class': 'dashicons dashicons-no-alt' }));

      $thumbWrapper.append($image);
      $slide.append($thumbWrapper, $remove);
      $container.append($slide);
    }

    $('#hram-home-slider-add').on('click', function (event) {
      event.preventDefault();

      if (frame) {
        frame.open();
        return;
      }

      frame = wp.media({
        title: hramHomeSliderAdmin.i18n.frameTitle,
        button: {
          text: hramHomeSliderAdmin.i18n.frameButton
        },
        multiple: true
      });

      frame.on('select', function () {
        var selection = frame.state().get('selection');

        selection.each(function (model) {
          addAttachment(model.toJSON());
        });

        updateInput();
      });

      frame.on('open', function () {
        var ids = $input.val();

        if (!ids) {
          return;
        }

        var selection = frame.state().get('selection');
        var idArray = ids.split(',');

        idArray.forEach(function (id) {
          var attachment = wp.media.attachment(id);

          if (!attachment) {
            return;
          }

          attachment.fetch();
          selection.add(attachment);
        });
      });

      frame.open();
    });

    $container.on('click', '.hram-home-slider-slide__remove', function (event) {
      event.preventDefault();

      $(this).closest('.hram-home-slider-slide').remove();
      updateInput();
    });

    $container.sortable({
      items: '.hram-home-slider-slide',
      cursor: 'move',
      placeholder: 'hram-home-slider-slide hram-home-slider-slide--placeholder',
      update: updateInput
    });

    updateInput();
  });
})(jQuery);
