/**
 * @file
 * Defines Javascript behaviors for the W3CSS Theme.
 * https://www.drupal.org/node/3158256
 */
((Drupal, once) => {
  'use strict';

  const cssStyles = {
    overflow: 'hidden',
    height: '0',
    paddingTop: '0',
    paddingBottom: '0',
    marginTop: '0',
    marginBottom: '0'
  };

  const slideUp = (target, duration = 500) => {
    target.style.transitionProperty = 'height, margin, padding';
    target.style.transitionDuration = `${duration}ms`;
    target.style.boxSizing = 'border-box';
    target.style.height = `${target.offsetHeight}px`;
    target.offsetHeight;
    target.classList.remove('show');

    Object.keys(cssStyles)
      .forEach(style => {
        target.style[style] = cssStyles[style];
      });

    setTimeout(() => {
      target.style.display = 'none';
      removeStyles(target);
    }, duration);
  }

  const removeStyles = (target) => {
    const stylesToRemove = ['height', 'paddingTop', 'paddingBottom', 'marginTop', 'marginBottom', 'overflow', 'transitionDuration', 'transitionProperty'];
    stylesToRemove.forEach(style => target.style.removeProperty(style));
  }

  const slideDown = (target, duration = 500) => {
    target.style.removeProperty('display');
    let display = window.getComputedStyle(target)
      .display;

    if (display === 'none') display = 'block';

    target.style.display = display;
    let height = target.offsetHeight;

    Object.keys(cssStyles)
      .forEach(style => {
        target.style[style] = cssStyles[style];
      });

    target.offsetHeight;
    target.style.boxSizing = 'border-box';
    target.style.transitionProperty = "height, margin, padding";
    target.style.transitionDuration = `${duration}ms`;
    target.style.height = `${height}px`;
    target.classList.add('show');

  ['paddingTop', 'paddingBottom', 'marginTop', 'marginBottom'].forEach(property => {
      target.style.removeProperty(property);
    });

    setTimeout(() => {
    ['height', 'overflow', 'transitionDuration', 'transitionProperty'].forEach(property => target.style.removeProperty(property));
    }, duration);
  }

  const slideToggle = (target, duration = 500) => {
    if (!target.classList.contains('show')) {
      return slideDown(target, duration);
    } else {
      return slideUp(target, duration);
    }
  }

  // Get current width
  const getCurrentWidth = () => window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  // Used for fixed menu.
  let mainMenuBg;
  const mainNavWrapper = document.querySelector('.main-navigation-wrapper');
  if (mainNavWrapper) {
    mainMenuBg = window.getComputedStyle(mainNavWrapper)
      .backgroundColor;
  }

  // Used for fixed menu.
  let origOffsetY;
  const mainNavigation = document.querySelector('#main-navigation-h');
  if (mainNavigation && mainNavigation.querySelector('.ul-parent')) {
    origOffsetY = mainNavigation.offsetTop;
  }

  // Add flex position to the main menu at scroll.
  const scrollWindow = () => {
    if (mainNavigation) {
      mainNavigation.classList.toggle('w3-sticky', window.scrollY > origOffsetY);
    }
  };

  // Add/remove css classes according to screen changes.
  const resizeMedia = () => {
    const currentWidth = getCurrentWidth();
    const removeStyles = (selector) => {
      document.querySelectorAll(selector)
        .forEach(el => {
          el.classList.remove('w3-show');
          el.removeAttribute('style');
        });
    };

    if (currentWidth >= 993) {
    ['.ul-parent', '.ul-child', '.ul-responsive-h .ul-parent', '.ul-responsive-h .ul-child', '#main-navigation-h .ul-parent'].forEach(selector => removeStyles(selector));

      document.querySelectorAll('#main-navigation-h .ul-child')
        .forEach(el => {
          el.style.backgroundColor = mainMenuBg;
        });
    }
  };

  let displayVertical;
  const mainNavVertical = document.querySelector('#main-navigation-v');
  if (mainNavVertical) {
    displayVertical = window.getComputedStyle(mainNavVertical)
      .display;
  }

  const hideChildren = (childElement) => {
    slideUp(childElement);
  }

  const hideParents = (ulParent, ulChildren = '') => {

    if (!ulParent.classList.contains('show')) {
      slideDown(ulParent);
    } else {
      slideUp(ulParent);
      if (ulChildren) {
        ulChildren.forEach((ulChild) => {
          hideChildren(ulChild);
        });
      }
    }

  }

  const navClickListener = (selector, callback) => {
    const element = document.querySelector(selector);
    if (element) {
      element.addEventListener('click', callback);
    }
  };

  const hideElements = (parentSelector, childrenSelector) => {
    const ulParent = document.querySelector(parentSelector);
    const ulChildren = document.querySelectorAll(childrenSelector);
    hideParents(ulParent, ulChildren);
  };

  navClickListener('#main-navigation-h .mobile-nav', () => {
    hideElements('#main-navigation-h .ul-parent', '#main-navigation-h .ul-parent .ul-child');
  });

  navClickListener('.ul-responsive-h .mobile-nav-responsive', () => {
    hideElements('.ul-responsive-h .ul-parent', '.ul-responsive-h .ul-parent .ul-child');
  });

  navClickListener('#main-navigation-v #close-nav', () => {
    const nav = document.getElementById('main-navigation-v');
    nav.style.display = 'none';
    const childElements = document.querySelectorAll('#main-navigation-v .ul-parent li .ul-child');
    childElements.forEach(hideChildren);
  });

  navClickListener('#open-nav-inner', () => {
    const nav = document.getElementById('main-navigation-v');
    nav.style.display = 'block';
    const displayVertical = window.getComputedStyle(nav)
      .display;
  });

  const ulToggler = (element) => {
    //  let getDepth = calculateDepth(element);
    let menuUl = element.parentNode.querySelector('.ul-child');

    if (!menuUl.classList.contains('show')) {
      slideDown(menuUl);
    } else {
      slideUp(menuUl);
    }

  }

  const addClickListener = (selector) => {
    document.querySelectorAll(selector)
      .forEach(function(element) {

        element.addEventListener('click', function(event) {

          if (element.classList.contains('link-disabled')) {
            event.preventDefault();
            event.stopPropagation();
          }

          if (selector == '.tMenu') {
            const currentWidth = getCurrentWidth();

            // console.log(display);

            if ((currentWidth <= 992) || (displayVertical === 'block')) {
              ulToggler(element);
            }

          } else if (selector == '.tMenu-v') {
            ulToggler(element);

          } else {
            ulToggler(element);
          }

        });

      });
  } // End of addClickListener

  const selectors = [
    '.tMenu'

    , '.tMenu-v'

    , '.clickable-dropdown-menu-fa-down'

    , '.clickable-dropdown-menu-fa-down-v'

    , '.clickable-dropdown-menu-fa-down-h'
  ];

  selectors.forEach(selector => {
    if (document.querySelector(selector)) {
      addClickListener(selector);
    }
  });

  Drupal.behaviors.WindowSize = {
    attach: function(context, settings) {

      getCurrentWidth();
      resizeMedia();
      window.addEventListener('resize', resizeMedia, getCurrentWidth);
      window.addEventListener('scroll', scrollWindow);

    }
  };

})(Drupal, once);
