/* global jQuery, document */
// import ClipBoard from 'clipboard';
// import Cookies from 'js-cookie';
// import debounce from 'lodash/debounce';
// import hoverintent from 'hoverintent';
// import map from 'lodash/fp/map';

(($, Drupal) => {
  /* global window, Drupal */
  /* eslint-disable no-param-reassign */
  Drupal.behaviors.savethebwTabs = {
    attach: () => {
      $('body').once('savethebw-tabs').each(() => {
        
      });
    },
  };
})(jQuery, Drupal);
