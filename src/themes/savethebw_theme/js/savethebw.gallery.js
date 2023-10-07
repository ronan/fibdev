(function (Drupal, drupalSettings, $) {
  Drupal.behaviors.savethebw_justified_gallery = {
    attach: function (context, settings) {

      $('.paragraph--type-image-gallery').once('justified-gallery').each(function() {
        $(".field--name-field-images", this).justifiedGallery({
          rowHeight: 280,
          maxRowHeight: 300,
          margins: 10,
          border: 10,
          selector: 'div.field__item',
          imgSelector: '> img',
          lastRow: 'center',
          captions: true,
          randomize: false,
        });
      });
    }
  }
})(Drupal, drupalSettings, jQuery);
