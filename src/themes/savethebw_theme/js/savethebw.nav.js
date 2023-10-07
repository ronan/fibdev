/* global window, Drupal, document, MutationObserver, jQuery */
import map from 'lodash/fp/map';

(($, Drupal) => {
  /* eslint-disable no-param-reassign */
  Drupal.behaviors.savethebwNav = {
    attach: () => {
      $('body').once('savethebw-nav').each(() => {
        // If the page is wrappered in an mmenu container, copy the top padding
        // to the top attribute of the sticky header. This occurs when the admin
        // bar is present, so this fix keeps the nav menu from disappearing
        // under the admin bar.
        const mmPage = document.querySelector('.mm-page');
        if (mmPage && window.MutationObserver) {
          /* eslint-disable no-new */
          const callback = (mutationsList) => {
            map((entry) => {
              const paddingTop = entry.target.style['padding-top'];
              if (paddingTop) {
                const sticky = document.querySelector('header.sticky-top');
                if (sticky) {
                  sticky.style.top = paddingTop;
                }
              }
            }, mutationsList);
          };
          const observer = new MutationObserver(callback);
          observer.observe(mmPage, { attributes: true, childList: false, subtree: false });
        }
      });
    },
  };
})(jQuery, Drupal);
