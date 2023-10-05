/* global jQuery, document */
// import ClipBoard from 'clipboard';
import Cookies from 'js-cookie';
// import debounce from 'lodash/debounce';
// import hoverintent from 'hoverintent';
import map from 'lodash/fp/map';
import includes from 'lodash/includes';
import Player from '@vimeo/player';

(($, Drupal) => {
  /* global window, Drupal */
  /* eslint-disable no-param-reassign */
  Drupal.behaviors.savethebw = {
    attach: () => {
      $('body').once('savethebw').each(() => {
        // Autostart videos in modals
        const tag = document.createElement('script');
        tag.id = 'iframe-yt-api';
        tag.src = 'https://www.youtube.com/iframe_api';
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        window.onYouTubeIframeAPIReady = () => {
          const youtubeContainers = document.querySelectorAll('.modal .field--name-field-video');
          window.ytPlayers = {};
          window.vimeoPlayers = {};
          map((container) => {
            const modal = $(container).closest('.modal');
            const { id } = modal[0];
            const iframe = container.querySelector('iframe');
            if (includes(iframe.getAttribute('src'), 'youtube')) {
              /* global YT */
              window.ytPlayers[id] = new YT.Player(iframe, { events: {} });
            } else if (includes(iframe.getAttribute('src'), 'vimeo')) {
              window.vimeoPlayers[id] = new Player(iframe);
            }
          }, youtubeContainers);
          const modalToggles = document.querySelectorAll('[data-toggle="modal"]');
          map((toggle) => {
            /* eslint-disable no-param-reassign */
            toggle.onclick = () => {
              const target = toggle.getAttribute('data-target');
              const $modal = $(target);
              const id = $modal.attr('id');
              if (window.ytPlayers[id]) window.ytPlayers[id].playVideo();
              if (window.vimeoPlayers[id]) window.vimeoPlayers[id].play();
            };
          }, modalToggles);
          $(document).on('hide.bs.modal', (evt) => {
            const id = $(evt.target).attr('id');
            if (window.ytPlayers[id]) window.ytPlayers[id].pauseVideo();
            if (window.vimeoPlayers[id]) window.vimeoPlayers[id].pause();
          });
        };

        // Move modals to the end of the body to avoid z-index issues.
        const bsModals = document.querySelectorAll('.modal');
        map(modal => document.body.appendChild(modal), bsModals);

        // Get the color of the first wave and add a body class
        const components = document.querySelector('.field--name-field-components');
        let waveBodyClass = 'first-wave-white';
        if (components) {
          const firstComponent = components.querySelector('.paragraph');
          if (firstComponent && $(firstComponent).hasClass('color-gray')) {
            waveBodyClass = 'first-wave-gray';
          }
        }
        document.body.classList.add(waveBodyClass);

        // Popups are shown once per day, or when viewing the popup content type
        const popup = document.querySelector('.node--type-popup.node--view-mode-overlay.modal');
        if (popup) {
          const id = popup.getAttribute('id');
          if (popup.querySelector('img')) {
            document.body.classList.add('popup-has-image');
          } else {
            document.body.classList.add('popup-no-image');
          }
          if (!Cookies.get(`${id}`) || $(document.body).hasClass('node--type-popup')) {
            $(popup).modal('show');
          }
        }
        $('.node--type-popup.node--view-mode-overlay.modal').on('hidden.bs.modal', (evt) => {
          const id = evt.target.getAttribute('id');
          Cookies.set(`${id}`, Date.now(), { expires: 1 });
          if ($(document.body).hasClass('node--type-popup')) Cookies.remove(`${id}`);
        });

        $('.paragraph--type-image.paragraph--view-mode-images-download').once().each(function (event) {
          $('.field--name-field-image a').attr('target', '_blank');
        });
      });

      // Tab behavior
      // Check for a hash value on page load and activate a matching tab.
      if (window.location.hash) {
        const hashVal = window.location.hash.replace('#', '');
        if (document.querySelector(`a[data-toggle="tab"][data-slug="${hashVal}"]`)) {
          const $activeTab = $(`a[data-toggle="tab"][data-slug="${hashVal}"]`);
          $activeTab.tab('show');
        }
      }
      // When a bootstrap tab is activated, update the hash value
      /* global history, location */
      $('a[data-toggle="tab"]').on('shown.bs.tab', (evt) => {
        const slug = $(evt.target).data('slug');
        const hashVal = window.location.hash.replace('#', '');
        if (slug !== hashVal) {
          /* eslint-disable no-restricted-globals */
          if (history.pushState) {
            history.pushState(null, null, `#${slug}`);
          } else {
            location.hash = `#${slug}`;
          }
        }
      });
      // Update the active bootstrap tab when clicking back
      $(window).on('popstate', () => {
        const hashVal = window.location.hash.replace('#', '');
        const displayedTab = document.querySelector('a.active[data-toggle="tab"]');
        if (displayedTab) {
          const slug = $(displayedTab).data('slug');
          if (slug !== hashVal) {
            if (hashVal) {
              const $activeTab = $(`a[data-toggle="tab"][data-slug="${hashVal}"]`);
              $activeTab.tab('show');
            } else {
              const firstTab = document.querySelector('a[data-toggle="tab"]');
              $(firstTab).click();
            }
          }
        }
      });

      const autosubmitSort = () => {
        $('.form-item-sort-bef-combine select').on('change', (evt) => {
          $(evt.target)
            .closest('form')
            .find('.form-actions > [type="submit"]:first-of-type')
            .click();
        });
      };
      autosubmitSort();
      $(window).ajaxComplete(autosubmitSort);
    },
  };
})(jQuery, Drupal);
