/**
 * @file
 * Defines Javascript behaviors for the W3CSS Theme.
 */

(function (Drupal, once) {
  'use strict';

  // Duration of the menu slide, in milliseconds.
  const SLIDE_DURATION = 350;

  // A close match for the curve 0.5 - cos(p * PI) / 2 the menus have always had.
  const SLIDE_EASING = 'cubic-bezier(0.37, 0, 0.63, 1)';

  // The box properties a slide animates.
  const SLIDE_PROPERTIES = ['height', 'paddingTop', 'paddingBottom', 'marginTop', 'marginBottom'];

  // Elements whose next sibling is the submenu they open.
  const SUBMENU_TRIGGERS = '.d8w3css-menu__link-toggle, .d8w3css-menu__link-toggle-vertical, .d8w3css-menu__arrow-toggle';

  // Old selector to BEM class. The JS selects only the BEM classes. Templates
  // copied into a sub-theme or site still print only the old names, so
  // adoptLegacyHooks() adds the BEM class to those elements.
  const LEGACY_HOOKS = {
    '#main-navigation-h': 'd8w3css-main-nav--horizontal',
    '#main-navigation-v': 'd8w3css-main-nav--vertical',
    '.main-navigation-wrapper': 'd8w3css-main-nav',
    '#close-nav': 'd8w3css-main-nav__close',
    '#open-nav-inner': 'd8w3css-main-nav__open',
    '.mobile-nav-responsive': 'd8w3css-responsive-nav__toggle',
    '.mobile-nav': 'd8w3css-mobile-nav__toggle',
    '.ul-responsive-h': 'd8w3css-responsive-nav',
    '.ul-parent': 'd8w3css-menu__list',
    '.ul-child': 'd8w3css-menu__submenu',
    '.tMenu-v': 'd8w3css-menu__link-toggle-vertical',
    '.tMenu': 'd8w3css-menu__link-toggle',
    '.clickable-dropdown-menu-fa-down-h': 'd8w3css-menu__arrow-toggle',
    '.clickable-dropdown-menu-fa-down-v': 'd8w3css-menu__arrow-toggle',
    '.clickable-dropdown-menu-fa-down': 'd8w3css-menu__arrow-toggle',
    '.dropdown-arrow': 'd8w3css-menu__arrow',
    '.top-container-inner': 'd8w3css-top-region__inner',
    '.main-container-inner': 'd8w3css-main-region__inner',
    '.bottom-container-inner': 'd8w3css-bottom-region__inner',
    '.footer-container-inner': 'd8w3css-footer-region__inner',
    '.social-media': 'd8w3css-social-media',
    '#footer-menu': 'd8w3css-footer-menu',
    '.main-box': 'd8w3css-main-box',
    'nav.breadcrumb': 'd8w3css-breadcrumb'
  };

  // Marks a submenu opened by a click. The old name 'show' is kept beside it.
  const SUBMENU_OPEN = 'd8w3css-menu__submenu--open';

  // The screen sizes the body is marked with.
  const SCREEN_SIZES = ['small', 'medium', 'large'];

  // Slides in progress, by element.
  const slides = new WeakMap();

  // Numbers the ids given to submenus for aria-controls.
  let submenuCount = 0;

  /**
   * Adds the BEM class to elements that only have the old name.
   *
   * @param {Document|Element} context
   *   Where to look.
   */
  function adoptLegacyHooks(context) {
    Object.keys(LEGACY_HOOKS).forEach(function (selector) {
      context.querySelectorAll(selector).forEach(function (element) {
        element.classList.add(LEGACY_HOOKS[selector]);
      });
    });
  }

  adoptLegacyHooks(document);

  // Used for fixed menu.
  let origOffsetY;
  const mainNavigation = document.querySelector('.d8w3css-main-nav--horizontal');

  // In case the main menu not printed. Measured here, before any behavior runs
  // and before the toolbar can pad the page, because this offset is the
  // scroll position at which the menu sticks.
  if (mainNavigation && mainNavigation.querySelector('.d8w3css-menu__list')) {
    origOffsetY = mainNavigation.offsetTop;
  }

  /**
   * Returns every element matching a selector, as an array.
   *
   * @param {string} selector
   *   The selector.
   *
   * @return {Element[]}
   *   The matching elements.
   */
  function all(selector) {
    return Array.from(document.querySelectorAll(selector));
  }

  /**
   * Reads a computed style from the first element matching a selector.
   *
   * @param {string} selector
   *   The selector.
   * @param {string} property
   *   The CSS property, hyphenated.
   *
   * @return {string|undefined}
   *   The computed value, or undefined when no element matches.
   */
  function computedStyle(selector, property) {
    const element = document.querySelector(selector);
    return element ? window.getComputedStyle(element).getPropertyValue(property) : undefined;
  }

  /**
   * Sets an inline style, leaving the element alone when there is no value.
   *
   * @param {Element} element
   *   The element.
   * @param {string} property
   *   The CSS property, hyphenated.
   * @param {string|undefined} value
   *   The value, from computedStyle().
   */
  function setStyle(element, property, value) {
    if (value !== undefined) {
      element.style.setProperty(property, value);
    }
  }

  /**
   * Whether the visitor asked for reduced motion.
   *
   * @return {boolean}
   *   TRUE when animation must be skipped.
   */
  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /**
   * Whether an element has no box on the page.
   *
   * @param {Element} element
   *   The element.
   *
   * @return {boolean}
   *   TRUE when the element is not rendered.
   */
  function isHidden(element) {
    return !(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
  }

  /**
   * Whether an element is open, or on its way to being open.
   *
   * @param {Element} element
   *   The element.
   *
   * @return {boolean}
   *   TRUE when the element is, or will end up, visible.
   */
  function isOpen(element) {
    const running = slides.get(element);
    return running ? running.show : !isHidden(element);
  }

  /**
   * Reads the box properties a slide animates.
   *
   * @param {Element} element
   *   The element.
   *
   * @return {Object}
   *   A keyframe holding the current values.
   */
  function readBox(element) {
    const style = window.getComputedStyle(element);
    const box = {};
    SLIDE_PROPERTIES.forEach(function (property) {
      box[property] = style[property];
    });
    return box;
  }

  /**
   * Stops a slide where it is, without finishing it.
   *
   * @param {Element} element
   *   The element.
   */
  function stopSlide(element) {
    const running = slides.get(element);
    if (running) {
      slides.delete(element);
      running.animation.cancel();
      element.style.overflow = running.overflow;
    }
  }

  /**
   * Slides an element open or closed. Does nothing if it is already there.
   *
   * An interrupted slide reverses from where it is. With reduced motion, the
   * element goes straight to its final state.
   *
   * @param {Element} element
   *   The element.
   * @param {boolean} show
   *   TRUE to open the element, FALSE to close it.
   */
  function slide(element, show) {
    const running = slides.get(element);
    // Only the element's own display counts here. A submenu inside a closed
    // parent still has to end up closed, for when that parent opens again.
    if (running ? running.show === show : (window.getComputedStyle(element).display === 'none') !== show) {
      return;
    }

    const interruptedAt = running ? readBox(element) : null;
    stopSlide(element);

    if (show) {
      element.style.display = '';
      if (window.getComputedStyle(element).display === 'none') {
        element.style.display = 'block';
      }
    }

    // There is nothing to animate inside a parent that is not rendered.
    if (prefersReducedMotion() || !element.getClientRects().length) {
      if (!show) {
        element.style.display = 'none';
      }
      return;
    }

    const natural = readBox(element);
    const closed = {};
    SLIDE_PROPERTIES.forEach(function (property) {
      closed[property] = '0px';
    });

    const overflow = element.style.overflow;
    element.style.overflow = 'hidden';
    const animation = element.animate(
      [interruptedAt || (show ? closed : natural), show ? natural : closed],
      { duration: SLIDE_DURATION, easing: SLIDE_EASING }
    );
    slides.set(element, { animation, overflow, show });
    animation.onfinish = function () {
      slides.delete(element);
      element.style.overflow = overflow;
      if (!show) {
        element.style.display = 'none';
      }
    };
  }

  /**
   * Publishes the state of every menu in aria-expanded.
   */
  function syncExpanded() {
    all(SUBMENU_TRIGGERS).forEach(function (trigger) {
      const submenu = trigger.nextElementSibling;
      if (submenu) {
        if (!submenu.id) {
          submenuCount++;
          submenu.id = 'd8w3css-submenu-' + submenuCount;
        }
        trigger.setAttribute('aria-controls', submenu.id);
        trigger.setAttribute('aria-expanded', submenu.classList.contains(SUBMENU_OPEN) ? 'true' : 'false');
      }
    });
    [
      ['.d8w3css-mobile-nav__toggle', '.d8w3css-main-nav--horizontal .d8w3css-menu__list'],
      ['.d8w3css-responsive-nav__toggle', '.d8w3css-responsive-nav .d8w3css-menu__list']
    ].forEach(function (pair) {
      const open = all(pair[1]).some(isOpen);
      all(pair[0]).forEach(function (trigger) {
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });
  }

  /**
   * Lets a non-interactive element carry aria-expanded and take the keyboard.
   *
   * @param {Element} element
   *   The element.
   * @param {string} label
   *   The translated accessible name.
   */
  function makeButton(element, label) {
    element.setAttribute('role', 'button');
    element.setAttribute('tabindex', '0');
    if (!element.hasAttribute('aria-label')) {
      element.setAttribute('aria-label', label);
    }
    element.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        element.click();
      }
    });
  }

  /**
   * Closes submenus.
   *
   * @param {Element[]} submenus
   *   The submenus.
   */
  function closeSubmenus(submenus) {
    submenus.forEach(function (submenu) {
      submenu.classList.remove('show', SUBMENU_OPEN);
      slide(submenu, false);
    });
  }

  /**
   * Opens or closes a whole menu from its mobile button.
   *
   * @param {string} parents
   *   Selector for the top level of the menu.
   */
  function toggleMenu(parents) {
    const menus = all(parents);
    if (menus.some(isHidden)) {
      menus.forEach(function (menu) {
        slide(menu, true);
      });
    }
    else {
      menus.forEach(function (menu) {
        slide(menu, false);
      });
      closeSubmenus(all(parents + ' li .d8w3css-menu__submenu'));
    }
    syncExpanded();
  }

  // Add flex position to the main menu at scroll.
  const scrollWindow = function () {
    if (mainNavigation) {
      if (window.scrollY > origOffsetY) {
        mainNavigation.classList.add('w3-sticky');
      }
      else {
        mainNavigation.classList.remove('w3-sticky');
      }
    }
  };

  /**
   * Removes the inline styles of the elements matching a selector.
   *
   * @param {string} selector
   *   The selector.
   */
  function clearInlineStyles(selector) {
    all(selector).forEach(function (element) {
      stopSlide(element);
      element.removeAttribute('style');
    });
  }

  /**
   * Adds w3-f-display to a container when it has more than one column.
   *
   * @param {string} container
   *   Selector for the container.
   */
  function addFlexDisplay(container) {
    if (all(container + ' > :is(div, main, aside)').length > 1) {
      all(container).forEach(function (element) {
        element.classList.add('w3-f-display');
      });
    }
  }

  /**
   * Marks the body with the current screen size.
   *
   * @param {string} size
   *   One of SCREEN_SIZES.
   */
  function setScreenClass(size) {
    SCREEN_SIZES.forEach(function (name) {
      document.body.classList.toggle(name + '-screen', name === size);
      document.body.classList.toggle('d8w3css-page--' + name + '-screen', name === size);
    });
  }

  // Add and remove classes on window resize.
  const mediaSize = function () {
    const currentWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    const mainMenuChild = computedStyle('.d8w3css-main-nav--horizontal', 'background-color');

    if (currentWidth >= 993) {
      setScreenClass('large');
      all('.d8w3css-menu__list, .d8w3css-menu__submenu').forEach(function (element) {
        element.classList.remove('w3-show');
      });
      clearInlineStyles('.d8w3css-responsive-nav .d8w3css-menu__list');
      clearInlineStyles('.d8w3css-responsive-nav .d8w3css-menu__submenu');
      clearInlineStyles('.d8w3css-main-nav--horizontal .d8w3css-menu__list');
      clearInlineStyles('.d8w3css-main-nav--horizontal .d8w3css-menu__submenu');
      all('.d8w3css-main-nav--horizontal .d8w3css-menu__submenu').forEach(function (element) {
        setStyle(element, 'background-color', mainMenuChild);
      });
      addFlexDisplay('.d8w3css-top-region__inner');
      addFlexDisplay('.d8w3css-main-region__inner > .w3-clear');
      addFlexDisplay('.d8w3css-bottom-region__inner');
      addFlexDisplay('.d8w3css-footer-region__inner');
    }
    else {
      // Remove the match height on medium/small screen.
      all('.d8w3css-top-region__inner, .d8w3css-main-region__inner > .w3-clear, .d8w3css-bottom-region__inner, .d8w3css-footer-region__inner').forEach(function (element) {
        element.classList.remove('w3-f-display');
      });
    }

    if ((currentWidth >= 601) && (currentWidth <= 992)) {
      setScreenClass('medium');
    }

    if (currentWidth <= 600) {
      setScreenClass('small');
    }

    syncExpanded();
  };

  /**
   * Helper function to show/hide dropdown menus.
   *
   * @param {Element} trigger
   *   The clicked element.
   */
  function showHide(trigger) {
    const submenu = trigger.nextElementSibling;
    if (submenu && submenu.classList.contains(SUBMENU_OPEN)) {
      submenu.classList.remove('show', SUBMENU_OPEN);
      slide(submenu, false);
    }
    else {
      const list = trigger.parentElement && trigger.parentElement.parentElement;
      if (list) {
        closeSubmenus(Array.from(list.querySelectorAll(':scope li .d8w3css-menu__submenu')).filter(function (element) {
          return element !== submenu;
        }));
      }
      if (submenu) {
        submenu.classList.add('show', SUBMENU_OPEN);
        slide(submenu, true);
        // A submenu open only because of hover has to stay open afterwards.
        if (!submenu.style.display) {
          submenu.style.display = window.getComputedStyle(submenu).display;
        }
      }
    }
    syncExpanded();
  }

  /**
   * Counts the ancestors of an element matching a selector.
   *
   * @param {Element} element
   *   The element.
   * @param {string} selector
   *   The selector.
   *
   * @return {number}
   *   The number of matching ancestors.
   */
  function countAncestors(element, selector) {
    let depth = 0;
    let ancestor = element.parentElement && element.parentElement.closest(selector);
    while (ancestor) {
      depth++;
      ancestor = ancestor.parentElement && ancestor.parentElement.closest(selector);
    }
    return depth;
  }

  /**
   * Adds classes to elements.
   *
   * @param {Element[]} elements
   *   The elements.
   * @param {string} classes
   *   The classes, separated by spaces.
   */
  function addClasses(elements, classes) {
    elements.forEach(function (element) {
      element.classList.add(...classes.split(' '));
    });
  }

  /**
   * Gives Ajax content from copied templates its BEM classes.
   */
  Drupal.behaviors.d8w3cssLegacyHooks = {
    attach: function (context) {
      adoptLegacyHooks(context);
    }
  };

  /**
   * Add depth classes to menu items.
   */
  Drupal.behaviors.d8w3cssMenuDepth = {
    attach: function (context) {
      // Add class to any UL/LI according to the depth.
      once('d8w3css-menu-depth-list', 'ul', context).forEach(function (element) {
        element.classList.add('ul-' + countAncestors(element, 'ul'));
      });
      once('d8w3css-menu-depth-item', 'li', context).forEach(function (element) {
        element.classList.add('li-' + countAncestors(element, 'li'));
      });
    }
  };

  /**
   * Responsive menu behavior.
   */
  Drupal.behaviors.d8w3cssResponsiveMenu = {
    attach: function (context) {
      once('d8w3css-responsive-nav-toggle', '.d8w3css-responsive-nav__toggle', context).forEach(function (element) {
        makeButton(element, Drupal.t('Toggle menu'));
        element.addEventListener('click', function () {
          toggleMenu('.d8w3css-responsive-nav .d8w3css-menu__list');
        });
      });

      once('d8w3css-link-toggle-vertical', '.d8w3css-menu__link-toggle-vertical', context).forEach(function (element) {
        element.addEventListener('click', function (event) {
          event.preventDefault();
          showHide(element);
        });
      });

      once('d8w3css-arrow-toggle', '.d8w3css-menu__arrow-toggle', context).forEach(function (element) {
        makeButton(element, Drupal.t('Toggle submenu'));
        element.addEventListener('click', function () {
          showHide(element);
        });
      });
    }
  };

  /**
   * Main navigation behavior.
   */
  Drupal.behaviors.d8w3cssMainNav = {
    attach: function (context) {
      // On click show/hide the vertical main menu.
      once('d8w3css-main-nav-close', '.d8w3css-main-nav__close', context).forEach(function (element) {
        // Templates copied into a sub-theme may still print a div here.
        if (element.tagName !== 'BUTTON') {
          makeButton(element, Drupal.t('Close side menu'));
        }
        element.addEventListener('click', function () {
          const opener = document.querySelector('.d8w3css-main-nav__open');
          document.querySelector('.d8w3css-main-nav--vertical').style.display = 'none';
          closeSubmenus(all('.d8w3css-main-nav--vertical .d8w3css-menu__list li .d8w3css-menu__submenu'));
          syncExpanded();
          if (opener) {
            opener.setAttribute('aria-expanded', 'false');
            opener.focus();
          }
        });
      });

      once('d8w3css-main-nav-open', '.d8w3css-main-nav__open', context).forEach(function (element) {
        if (element.tagName !== 'BUTTON') {
          makeButton(element, Drupal.t('Open side menu'));
        }
        element.addEventListener('click', function () {
          const closer = document.querySelector('.d8w3css-main-nav__close');
          document.querySelector('.d8w3css-main-nav--vertical').style.display = 'block';
          element.setAttribute('aria-expanded', 'true');
          if (closer) {
            closer.focus();
          }
        });
      });

      // Show the mobile menu on click horizontal.
      once('d8w3css-mobile-nav-toggle', '.d8w3css-mobile-nav__toggle', context).forEach(function (element) {
        makeButton(element, Drupal.t('Toggle menu'));
        element.addEventListener('click', function () {
          toggleMenu('.d8w3css-main-nav--horizontal .d8w3css-menu__list');
        });
      });

      // On click expand the dropdown menu for small device.
      once('d8w3css-link-toggle', '.d8w3css-menu__link-toggle', context).forEach(function (element) {
        element.addEventListener('click', function (event) {
          event.preventDefault();
          const currentWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
          // A click with no pointer behind it (detail 0) came from the keyboard,
          // which cannot hover the wide menu open.
          if ((currentWidth <= 992) || (event.detail === 0) || (computedStyle('.d8w3css-main-nav--vertical', 'display') === 'block')) {
            showHide(element);
          }
        });
      });
    }
  };

  /**
   * General theme behavior.
   */
  Drupal.behaviors.d8w3cssTheme = {
    attach: function (context) {
      // Remove Fa class from social links.
      once('d8w3css-social-icon', '.d8w3css-social-media a > i', context).forEach(function (element) {
        element.classList.remove('fa');
      });

      // In case the admin toolbar search installed.
      once('d8w3css-toolbar-search', '#admin-toolbar-search-tab > div', context).forEach(function (element) {
        element.classList.remove('w3-section');
      });

      // Change the form color to match the footer color.
      const footerFormBg = computedStyle('.d8w3css-footer-menu', 'background-color');
      const footerFormTxt = computedStyle('.d8w3css-footer-menu', 'color');
      once('d8w3css-footer-form', '.d8w3css-footer-menu form', context).forEach(function (element) {
        setStyle(element, 'background-color', footerFormBg);
        setStyle(element, 'color', footerFormTxt);
      });

      // Add classes to search page.
      addClasses(once('d8w3css-search-advanced', '.search-form .search-advanced', context), 'w3-padding-large w3-border w3-bar w3-left-align w3-margin-bottom');
      addClasses(once('d8w3css-search-summary', '.search-form .search-advanced > summary', context), 'w3-button w3-bar w3-left-align');
      addClasses(once('d8w3css-search-help', '.search-form .search-help-link', context), 'w3-button w3-right w3-border w3-margin-top');
      addClasses(once('d8w3css-tabledrag-toggle', '.tabledrag-toggle-weight-wrapper button', context), 'w3-button');

      // Theme settings.
      const settingsDetails = once('d8w3css-settings-details', '#system-theme-settings details', context);
      settingsDetails.forEach(function (element) {
        element.removeAttribute('open');
      });
      addClasses(settingsDetails, 'w3-border w3-bar w3-left-align w3-margin-bottom');
      addClasses(once('d8w3css-settings-summary', '#system-theme-settings details > summary', context), 'w3-button w3-bar w3-left-align');
      addClasses(once('d8w3css-settings-wrapper', '#system-theme-settings details > .details-wrapper', context), 'w3-padding w3-left-align');

      // Disable top margin if breadcrumb exist.
      if (document.querySelector('.d8w3css-breadcrumb')) {
        addClasses(once('d8w3css-main-box', '.d8w3css-main-box', context), 'breadcrumb-found d8w3css-main-box--breadcrumb-found');
      }

      once('d8w3css-image-link', 'a > .w3-image', context).forEach(function (element) {
        element.parentElement.classList.add('d8-has-image', 'd8w3css-link--has-image');
      });

      mediaSize();
      once('d8w3css-window-listeners', 'html').forEach(function () {
        window.addEventListener('resize', mediaSize);
        window.addEventListener('scroll', scrollWindow);
      });
    }
  };

})(Drupal, once);
