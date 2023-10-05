/* global window, Drupal, document, jQuery */
import map from 'lodash/fp/map';
import scrollDetector from 'scroll-detector';
import trim from 'lodash/trim';

(($, Drupal) => {
  /* eslint-disable no-param-reassign */
  Drupal.behaviors.savethebwWave = {
    attach: () => {
      $('body').once('savethebw-wave').each(() => {
        // The wave animation is produced by transitioning between three
        // different static waves with linear calculations on variations for the
        // initial move, second vertical, and cubic bezier.
        const M1 = [0, 49.1];
        const M2 = [0, 28.1];
        const M3 = [0, 0];
        const V1 = 0;
        const V2 = 34.8;
        const V3 = 46.5;
        const c1 = [0, 0, -199.6, 7.4, -475.8, 80.5];
        const c2 = [0, 0, -169.5, 28.2, -475.8, 45.7];
        const c3 = [0, 0, -455.8, 34.3, -709.4, 46.9];
        const C1 = [479.1, 148.7, 213, 100.8, 0, 49.1];
        const C2 = [457.6, 94.2, 213, 79.8, 0, 28.1];
        const C3 = [325.3, 106.8, 170.4, 80.8, 0, 0];
        let paths = [];
        // Generate paths for first morph from initial static to mid-animation
        map((index) => {
          const M = [
            M1[0] + (((M2[0] - M1[0]) / 100) * (index + 1)),
            M1[1] + (((M2[1] - M1[1]) / 100) * (index + 1)),
          ];
          const V = V1 + (((V2 - V1) / 100) * (index + 1));
          const C = [
            C1[0] + (((C2[0] - C1[0]) / 100) * (index + 1)),
            C1[1] + (((C2[1] - C1[1]) / 100) * (index + 1)),
            C1[2] + (((C2[2] - C1[2]) / 100) * (index + 1)),
            C1[3] + (((C2[3] - C1[3]) / 100) * (index + 1)),
            C1[4] + (((C2[4] - C1[4]) / 100) * (index + 1)),
            C1[5] + (((C2[5] - C1[5]) / 100) * (index + 1)),
          ];
          const c = [
            c1[0] + (((c2[0] - c1[0]) / 100) * (index + 1)),
            c1[1] + (((c2[1] - c1[1]) / 100) * (index + 1)),
            c1[2] + (((c2[2] - c1[2]) / 100) * (index + 1)),
            c1[3] + (((c2[3] - c1[3]) / 100) * (index + 1)),
            c1[4] + (((c2[4] - c1[4]) / 100) * (index + 1)),
            c1[5] + (((c2[5] - c1[5]) / 100) * (index + 1)),
          ];
          paths.push(trim(`
            M${M.join(',')}
            V126
            h1260
            V${V}
            c${c.join(',')}
            C${C.join(',')}
            z
          `.replace(/\n/g, '').replace(/[ ]+/g, ' ')));
        }, [...Array(100).keys()]);

        // Generate paths for second morph
        map((index) => {
          const M = [
            M2[0] + (((M3[0] - M2[0]) / 100) * (index + 1)),
            M2[1] + (((M3[1] - M2[1]) / 100) * (index + 1)),
          ];
          const V = V2 + (((V3 - V2) / 100) * (index + 1));
          const C = [
            C2[0] + (((C3[0] - C2[0]) / 100) * (index + 1)),
            C2[1] + (((C3[1] - C2[1]) / 100) * (index + 1)),
            C2[2] + (((C3[2] - C2[2]) / 100) * (index + 1)),
            C2[3] + (((C3[3] - C2[3]) / 100) * (index + 1)),
            C2[4] + (((C3[4] - C2[4]) / 100) * (index + 1)),
            C2[5] + (((C3[5] - C2[5]) / 100) * (index + 1)),
          ];
          const c = [
            c2[0] + (((c3[0] - c2[0]) / 100) * (index + 1)),
            c2[1] + (((c3[1] - c2[1]) / 100) * (index + 1)),
            c2[2] + (((c3[2] - c2[2]) / 100) * (index + 1)),
            c2[3] + (((c3[3] - c2[3]) / 100) * (index + 1)),
            c2[4] + (((c3[4] - c2[4]) / 100) * (index + 1)),
            c2[5] + (((c3[5] - c2[5]) / 100) * (index + 1)),
          ];
          paths.push(trim(`
            M${M.join(',')}
            V126
            h1260
            V${V}
            c${c.join(',')}
            C${C.join(',')}
            z
          `.replace(/\n/g, '').replace(/[ ]+/g, ' ').replace(/,-/g, '-')));
        }, [...Array(100).keys()]);
        paths = [
          ...paths,
          ...paths.reverse(),
        ];
        // If there's an inline wave, add a scroll listener
        if (document.querySelector('.inline-wave svg path')) {
          const path = document.querySelector('.inline-wave svg path');
          const nextPath = () => {
            const d = paths.shift();
            path.setAttribute('d', d);
            paths.push(d);
            setTimeout(nextPath, window.doScrollBump ? 1 : 40);
          };
          nextPath();
          scrollDetector.on('scroll', () => {
            if (!window.doScrollBump) {
              window.doScrollBump = true;
              setTimeout(() => window.doScrollBump = false, 1000);
            }
          });
        }
      });
    },
  };
})(jQuery, Drupal);
