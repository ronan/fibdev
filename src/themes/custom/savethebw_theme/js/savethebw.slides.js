/* global window, Drupal, document, jQuery */
import map from 'lodash/fp/map';

(($, Drupal) => {
  /* eslint-disable no-param-reassign */
  Drupal.behaviors.savethebwSlides = {
    attach: () => {
      $('body').once('savethebw-slides').each(() => {
        const resizeMapSlides = () => {
          if (document.querySelector('.paragraph--type-map-slides')) {
            map((container) => {
              // Set the height on the map image container to the height of the
              // viewport
              const mapImages = container.querySelector('.field--name-savethebw-map-images');
              mapImages.style.height = `${window.innerHeight}px`;
              // Adjust the margin-top on the content container up by the same
              // amount
              const mapContent = container.querySelector('.field--name-field-slide-items');
              mapContent.style['margin-top'] = `-${window.innerHeight}px`;
              // Set the height on each slide to the full viewport
              map((slide) => {
                slide.style.height = `${window.innerHeight}px`;
              }, document.querySelectorAll('.field--name-field-slide-items > div'));
              // Vertically center each image
              map((slide) => {
                const img = slide.querySelector('img');
                if (window.innerHeight > img.offsetHeight) {
                  const diff = window.innerHeight - img.offsetHeight;
                  slide.querySelector('div').style.top = `${diff / 2}px`;
                }
              }, document.querySelectorAll('.paragraph--type-map-slide.paragraph--view-mode-images-only'));
            }, document.querySelectorAll('.paragraph--type-map-slides'));
          }
        };
        resizeMapSlides();
        window.addEventListener('resize', resizeMapSlides);

        if (document.querySelector('.paragraph--type-map-slides')) {
          const slideObserver = (entries) => {
            map((entry) => {
              // A little jQuery to grab the index to act on the corresponding
              // map slide image
              const position = $(entry.target).closest('.field__item').index();
              if (entry.isIntersecting) {
                if (position === 0) return;
                // Only add a scroll watcher when one doesn't exist already
                if (!entry.target.classList.contains('is-watched')) {
                  window.addEventListener('scroll', () => {
                    const { top } = entry.target.getBoundingClientRect();
                    // From the bottom of the screen to the middle of the screen
                    // is the transition zone, reaching full opacity by the time
                    // the content is vertically centered
                    if (top < window.innerHeight && top > window.innerHeight / 2) {
                      const half = window.innerHeight / 2;
                      // Percentage left to reach viewport midline
                      const remaining = (top - half) / half;
                      let opacity = 1 - remaining;
                      // Going for a more chunky transition ala Patagonia, so
                      // round to the nearest 10%
                      opacity = (Math.floor((opacity * 110) / 10) * 10) / 100;
                      $(entry.target)
                        .closest('.paragraph--type-map-slides')
                        .find(`.paragraph--type-map-slide.paragraph--view-mode-images-only:nth-of-type(${position + 1})`)
                        .css('opacity', opacity);
                    } else {
                      const $active = $(entry.target)
                        .closest('.paragraph--type-map-slides')
                        .find(`.paragraph--type-map-slide.paragraph--view-mode-images-only:nth-of-type(${position + 1})`);
                      const opacity = $active.css('opacity');
                      $active.css('opacity', Math.round(opacity));
                    }
                  });
                  entry.target.classList.add('is-watched');
                }
              }
            }, entries);
          };
          /* global IntersectionObserver */
          const observer = new IntersectionObserver(slideObserver, {});
          map((container) => {
            map((slide) => {
              observer.observe(slide);
              container.classList.add('is-loaded');
            }, container.querySelectorAll('.paragraph--type-map-slide.paragraph--view-mode-default'));
          }, document.querySelectorAll('.paragraph--type-map-slides'));
          // A hack to prevent the page loading at a scroll position within the map slides
          window.onbeforeunload = () => {
            window.scrollTo(0, 0);
          };
        }
      });
    },
  };
})(jQuery, Drupal);
