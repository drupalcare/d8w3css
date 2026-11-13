/**
 * @param Drupal
 * @param once
 * @file
 * Fades content in as it scrolls into view.
 */

(function (Drupal, once) {
  /**
   * Whether an element holds focus or the target of the URL #fragment.
   *
   * @param {Element} element
   *   A fading element.
   *
   * @return {boolean}
   *   TRUE when it must stay fully visible.
   */
  const isInUse = function (element) {
    return (
      element.matches(':focus-within') ||
      element.matches(':target') ||
      element.querySelector(':target') !== null
    );
  };

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
    // At the end of the page nothing can scroll further into view, so a box
    // low on the screen would otherwise stay partly transparent for good.
    const atPageEnd = windowBottom >= document.documentElement.scrollHeight - 1;

    elements.forEach(function (element) {
      // Focused content, and the target of the page's #fragment, is always
      // fully visible: focus must never sit on faded or hidden text.
      if (isInUse(element)) {
        element.style.transition = 'none';
        element.style.opacity = 1;
        return;
      }

      // An element with no box counts as being at the top of the page.
      const objectTop = element.getClientRects().length
        ? element.getBoundingClientRect().top + window.scrollY
        : 0;

      if (objectTop < windowBottom) {
        if (atPageEnd || objectTop < windowBottom - animationHeight) {
          element.style.transition = 'opacity 1s linear';
          element.style.opacity = 1;
        } else {
          element.style.transition = 'opacity 0.5s linear';
          element.style.opacity = (windowBottom - objectTop) * ratio;
        }
      } else {
        element.style.opacity = 0;
      }
    });
  };

  // Deprecated in 3.0, removed in the next major: the 2.x behavior name
  // d8w3cssFullOpacity reads and writes Drupal.behaviors.w3cssFade.
  // It is not enumerable, so Drupal.attachBehaviors() runs it once.
  if (!Object.hasOwn(Drupal.behaviors, 'd8w3cssFullOpacity')) {
    Object.defineProperty(Drupal.behaviors, 'd8w3cssFullOpacity', {
      configurable: true,
      get: () => Drupal.behaviors.w3cssFade,
      set: (value) => {
        Drupal.behaviors.w3cssFade = value;
      },
    });
  }

  /**
   * Full opacity on scroll behavior.
   */
  Drupal.behaviors.w3cssFade = {
    attach(context) {
      // Disable show on scroll if layout builder is active.
      if (!document.getElementById('layout-builder')) {
        once('w3css-fade', '.w3css-fade', context).forEach(function (element) {
          if (!isInUse(element)) {
            element.style.opacity = 0;
          }
        });
        fadeBox();
        once('w3css-fade-scroll', 'html').forEach(function () {
          window.addEventListener('scroll', fadeBox);
          // Focus or a #fragment can land in a faded region without a scroll.
          document.addEventListener('focusin', fadeBox);
          window.addEventListener('hashchange', fadeBox);
        });
      }
    },
  };
})(Drupal, once);
