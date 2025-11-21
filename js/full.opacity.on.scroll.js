/**
 * @file
 * Defines Javascript behaviors for the W3CSS Theme.
 */

(function (Drupal, $, once) {
  'use strict';

  let scrollOn = false;

  /**
   * Fade animation on scroll.
   */
  const fadeBox = function () {
    scrollOn = true;
    const animationHeight = $(window).innerHeight() * 0.25;
    const ratio = Math.round((1 / animationHeight) * 10000) / 10000;

    $('.d8-fade').each(function () {
      const objectTop = $(this).offset().top;
      const windowBottom = $(window).scrollTop() + $(window).innerHeight();

      if (objectTop < windowBottom) {
        if (objectTop < windowBottom - animationHeight) {
          $(this).css({
            transition: 'opacity 1s linear',
            opacity: 1
          });
        }
        else {
          $(this).css({
            transition: 'opacity 0.5s linear',
            opacity: (windowBottom - objectTop) * ratio
          });
        }
      }
      else {
        $(this).css('opacity', 0);
      }
    });
  };

  setInterval(function () {
    if (scrollOn) {
      scrollOn = false;
    }
  }, 100);

  /**
   * Full opacity on scroll behavior.
   */
  Drupal.behaviors.d8w3cssFullOpacity = {
    attach: function (context, settings) {
      // Disable show on scroll if layout builder is active.
      if (!document.getElementById("layout-builder")) {
        $(once('body', '.d8-fade', context)).css('opacity', 0);
        fadeBox();
        window.addEventListener('scroll', fadeBox);
      }
    }
  };

})(Drupal, jQuery, once);
