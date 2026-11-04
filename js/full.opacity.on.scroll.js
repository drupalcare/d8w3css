/**
 * @file
 * Fades content in as it scrolls into view.
 */

(function (Drupal, once) {
  'use strict';

  /**
   * Fade animation on scroll.
   */
  const fadeBox = function () {
    const elements = document.querySelectorAll('.w3css-fade');

    // Reduced motion: the content is simply visible, with no fading.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      elements.forEach(function (element) {
        element.style.transition = 'none';
        element.style.opacity = 1;
      });
      return;
    }

    const windowHeight = document.documentElement.clientHeight;
    const animationHeight = windowHeight * 0.25;
    const ratio = Math.round((1 / animationHeight) * 10000) / 10000;
    const windowBottom = window.scrollY + windowHeight;

    elements.forEach(function (element) {
      // An element with no box counts as being at the top of the page.
      const objectTop = element.getClientRects().length ? element.getBoundingClientRect().top + window.scrollY : 0;

      if (objectTop < windowBottom) {
        if (objectTop < windowBottom - animationHeight) {
          element.style.transition = 'opacity 1s linear';
          element.style.opacity = 1;
        }
        else {
          element.style.transition = 'opacity 0.5s linear';
          element.style.opacity = (windowBottom - objectTop) * ratio;
        }
      }
      else {
        element.style.opacity = 0;
      }
    });
  };

  /**
   * Full opacity on scroll behavior.
   */
  Drupal.behaviors.w3cssFullOpacity = {
    attach: function (context) {
      // Disable show on scroll if layout builder is active.
      if (!document.getElementById('layout-builder')) {
        once('w3css-fade', '.w3css-fade', context).forEach(function (element) {
          element.style.opacity = 0;
        });
        fadeBox();
        once('w3css-fade-scroll', 'html').forEach(function () {
          window.addEventListener('scroll', fadeBox);
        });
      }
    }
  };

})(Drupal, once);
