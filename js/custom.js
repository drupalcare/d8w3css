/**
 * @param Drupal
 * @param once
 * @file
 * Defines JavaScript behaviors for the W3CSS Theme.
 */

(function (Drupal, once) {
  // Duration of the menu slide, in milliseconds.
  const SLIDE_DURATION = 350;

  // A close match for the curve 0.5 - cos(p * PI) / 2 the menus have always had.
  const SLIDE_EASING = 'cubic-bezier(0.37, 0, 0.63, 1)';

  // The box properties a slide animates.
  const SLIDE_PROPERTIES = [
    'height',
    'paddingTop',
    'paddingBottom',
    'marginTop',
    'marginBottom',
  ];

  // Elements whose next sibling is the submenu they open.
  const SUBMENU_TRIGGERS =
    '.w3css-menu__link-toggle, .w3css-menu__link-toggle--vertical, .w3css-menu__arrow-toggle';

  // Marks a submenu opened by a click.
  const SUBMENU_OPEN = 'w3css-is-open';

  // The open submenus. The menu item of an open submenu carries the same state
  // class, so a lookup by state alone would also find the items.
  const OPEN_SUBMENUS = `.w3css-menu__submenu.${SUBMENU_OPEN}`;

  // A menu item that has a submenu.
  const MENU_ITEM = '.w3css-menu__item--expanded';

  // States of a menu item: its submenu is open by click or keyboard, is open by
  // hover, was closed with the pointer still on it, opens toward the other side.
  const ITEM_OPEN = 'w3css-is-open';
  const ITEM_HOVER = 'w3css-is-hovered';
  const ITEM_DISMISSED = 'w3css-is-dismissed';
  const ITEM_FLIPPED = 'w3css-is-flipped';

  // A flyout too long for the room below it scrolls. A scrolling list would clip
  // the flyouts inside it, so those are positioned against the viewport instead.
  const SUBMENU_SCROLLS = 'w3css-is-scrollable';
  const SUBMENU_DETACHED = 'w3css-is-detached';

  // Space kept between a flyout and the edge of the viewport, and the shortest a
  // scrolling flyout gets when its menu is near the bottom edge, in pixels.
  const VIEWPORT_GAP = 8;
  const MIN_FLYOUT_HEIGHT = 160;

  // The mobile buttons, each with the selector of the menu it opens.
  const MENU_TOGGLES = [
    [
      '.w3css-mobile-nav__toggle',
      '.w3css-main-nav--horizontal .w3css-menu__list',
    ],
    [
      '.w3css-menu__responsive-toggle',
      '.w3css-menu--responsive-h .w3css-menu__list',
    ],
  ];

  // The screen sizes the body is marked with.
  const SCREEN_SIZES = ['small', 'medium', 'large'];

  // Slides in progress, by element.
  const slides = new WeakMap();

  // Numbers the ids given to submenus for aria-controls.
  let submenuCount = 0;

  // Used for fixed menu.
  let origOffsetY;
  const mainNavigation = document.querySelector('.w3css-main-nav--horizontal');

  // In case the main menu not printed. Measured here, before any behavior runs
  // and before the toolbar can pad the page, because this offset is the
  // scroll position at which the menu sticks.
  if (mainNavigation && mainNavigation.querySelector('.w3css-menu__list')) {
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
    return element
      ? window.getComputedStyle(element).getPropertyValue(property)
      : undefined;
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
    return !(
      element.offsetWidth ||
      element.offsetHeight ||
      element.getClientRects().length
    );
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
   * Puts an element in its closed state.
   *
   * A submenu is closed by the stylesheet, and an inline display would stop
   * hover opening it again. The top level of a menu has no such default.
   *
   * @param {Element} element
   *   The element.
   */
  function hide(element) {
    if (element.classList.contains('w3css-menu__submenu')) {
      element.style.removeProperty('display');
    } else {
      element.style.display = 'none';
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
    if (
      running
        ? running.show === show
        : (window.getComputedStyle(element).display === 'none') !== show
    ) {
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
        hide(element);
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
      { duration: SLIDE_DURATION, easing: SLIDE_EASING },
    );
    slides.set(element, { animation, overflow, show });
    animation.onfinish = function () {
      slides.delete(element);
      element.style.overflow = overflow;
      if (!show) {
        hide(element);
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
          submenu.id = `w3css-submenu-${submenuCount}`;
        }
        const open = submenu.classList.contains(SUBMENU_OPEN);
        const item = submenu.parentElement;
        item.classList.toggle(ITEM_OPEN, open);
        trigger.setAttribute('aria-controls', submenu.id);
        trigger.setAttribute(
          'aria-expanded',
          open || item.classList.contains(ITEM_HOVER) ? 'true' : 'false',
        );
      }
    });
    MENU_TOGGLES.forEach(function (pair) {
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
    if (!element.hasAttribute('aria-label')) {
      element.setAttribute('aria-label', label);
    }
    // A real button already has the role, focus and the Enter and Space keys;
    // the patch below is for older sub-theme templates that still print a div.
    if (element.tagName === 'BUTTON') {
      return;
    }
    element.setAttribute('role', 'button');
    element.setAttribute('tabindex', '0');
    element.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        element.click();
      }
    });
  }

  /**
   * Names a submenu arrow after the link it sits beside.
   *
   * Every arrow would otherwise be called "Toggle submenu", so a screen
   * reader user could not tell them apart (WCAG 4.1.2).
   *
   * @param {Element} element
   *   The arrow toggle.
   *
   * @return {string}
   *   The translated accessible name.
   */
  function submenuToggleLabel(element) {
    const link = element.previousElementSibling;
    let title = '';
    if (link && link.tagName === 'A') {
      // Leave out visually hidden notes such as "(opens in a new tab)".
      const copy = link.cloneNode(true);
      copy.querySelectorAll('.visually-hidden').forEach(function (note) {
        note.remove();
      });
      title = copy.textContent.replace(/\s+/g, ' ').trim();
    }
    if (!title) {
      return Drupal.t('Toggle submenu');
    }
    // Drupal.t() escapes @title for HTML; an attribute needs the plain text.
    const decoder = document.createElement('textarea');
    decoder.innerHTML = Drupal.t('Toggle submenu of @title', {
      '@title': title,
    });
    return decoder.value;
  }

  /**
   * Whether a submenu opens beside its parent, over the page.
   *
   * @param {Element} submenu
   *   The submenu.
   *
   * @return {boolean}
   *   FALSE for the mobile and vertical menus, which open in the flow.
   */
  function isFlyout(submenu) {
    return ['absolute', 'fixed'].includes(
      window.getComputedStyle(submenu).position,
    );
  }

  /**
   * Returns the submenu of a menu item.
   *
   * @param {Element} item
   *   The menu item.
   *
   * @return {Element|null}
   *   The submenu, if there is one.
   */
  function childSubmenu(item) {
    return item.querySelector(':scope > .w3css-menu__submenu');
  }

  /**
   * Closes submenus.
   *
   * @param {Element[]} submenus
   *   The submenus.
   */
  function closeSubmenus(submenus) {
    submenus.forEach(function (submenu) {
      const item = submenu.parentElement;
      submenu.classList.remove(SUBMENU_OPEN);
      if (isFlyout(submenu) && item.matches(':hover')) {
        // Hover would show it again at once, so it stays down until the pointer
        // leaves.
        stopSlide(submenu);
        hide(submenu);
        item.classList.remove(ITEM_HOVER);
        item.classList.add(ITEM_DISMISSED);
      } else {
        slide(submenu, false);
      }
    });
  }

  /**
   * Closes a submenu and everything open inside it.
   *
   * @param {Element} submenu
   *   The submenu.
   */
  function closeBranch(submenu) {
    closeSubmenus(
      Array.from(submenu.querySelectorAll('.w3css-menu__submenu')).concat(
        submenu,
      ),
    );
  }

  /**
   * Measures the items of a submenu, whatever height it is limited to.
   *
   * @param {Element} submenu
   *   The submenu, already displayed.
   *
   * @return {number}
   *   The height of its items. scrollHeight would also count an open flyout.
   */
  function itemsHeight(submenu) {
    const first = submenu.firstElementChild;
    return first
      ? submenu.lastElementChild.getBoundingClientRect().bottom -
          first.getBoundingClientRect().top
      : 0;
  }

  /**
   * Makes a flyout scroll when its items do not fit the room it has.
   *
   * @param {Element} submenu
   *   The submenu.
   * @param {number} top
   *   Where the top of the submenu sits in the viewport.
   * @param {number} height
   *   The height of its items.
   */
  function fitHeight(submenu, top, height) {
    const room = Math.max(
      MIN_FLYOUT_HEIGHT,
      window.innerHeight - Math.max(0, top) - VIEWPORT_GAP,
    );
    submenu.classList.toggle(SUBMENU_SCROLLS, height > room);
    if (height > room) {
      submenu.style.setProperty('--w3css-menu-submenu-max', `${room}px`);
    } else {
      submenu.style.removeProperty('--w3css-menu-submenu-max');
    }
  }

  /**
   * Places a flyout that sits inside a scrolling submenu.
   *
   * It stays beside its item, on the side its parent went if that side has room,
   * and moves up from the bottom edge.
   *
   * @param {Element} submenu
   *   The submenu, already displayed.
   */
  function placeDetached(submenu) {
    const item = submenu.parentElement;
    const parent = item.parentElement.closest(MENU_ITEM);
    const rtl = window.getComputedStyle(item).direction === 'rtl';
    const box = item.getBoundingClientRect();
    const width = submenu.offsetWidth;
    const fitsRight = box.right + width <= document.documentElement.clientWidth;
    const fitsLeft = box.left - width >= 0;

    let toRight = rtl === parent.classList.contains(ITEM_FLIPPED);
    if (toRight ? !fitsRight && fitsLeft : !fitsLeft && fitsRight) {
      toRight = !toRight;
    }
    item.classList.toggle(ITEM_FLIPPED, toRight === rtl);

    const height = itemsHeight(submenu);
    const top = Math.max(
      VIEWPORT_GAP,
      Math.min(box.top, window.innerHeight - height - VIEWPORT_GAP),
    );
    const left = toRight ? box.right : box.left - width;
    submenu.style.inset = `${top}px auto auto ${left}px`;
    fitHeight(submenu, top, height);
  }

  /**
   * Keeps an open flyout on the screen.
   *
   * It opens toward the other side when it would leave the screen, and a nested
   * one moves up from the bottom edge. A nested one starts on the side its
   * parent went, so that it does not cover the parent. One still too long for
   * the screen scrolls.
   *
   * @param {Element} submenu
   *   The submenu, already displayed.
   */
  function placeSubmenu(submenu) {
    const item = submenu.parentElement;
    const parent = item.parentElement.closest(MENU_ITEM);
    let shift = 0;

    if (!isFlyout(submenu) || isHidden(submenu)) {
      item.classList.remove(ITEM_FLIPPED);
      submenu.classList.remove(SUBMENU_SCROLLS, SUBMENU_DETACHED);
      submenu.style.removeProperty('inset');
      submenu.style.removeProperty('--w3css-menu-submenu-max');
      return;
    }

    if (item.parentElement.closest(`.${SUBMENU_SCROLLS}`)) {
      submenu.classList.add(SUBMENU_DETACHED);
      placeDetached(submenu);
      return;
    }
    submenu.classList.remove(SUBMENU_DETACHED);
    submenu.style.removeProperty('inset');

    const outside = function () {
      const box = submenu.getBoundingClientRect();
      return (
        Math.max(0, -box.left) +
        Math.max(0, box.right - document.documentElement.clientWidth)
      );
    };

    item.classList.toggle(
      ITEM_FLIPPED,
      !!parent && parent.classList.contains(ITEM_FLIPPED),
    );
    const first = outside();
    if (first > 0) {
      item.classList.toggle(ITEM_FLIPPED);
      if (outside() >= first) {
        item.classList.toggle(ITEM_FLIPPED);
      }
    }

    // While sliding open its box is short, but its items have their full height.
    const height = itemsHeight(submenu);
    if (parent) {
      submenu.style.setProperty('--w3css-menu-submenu-shift', '0px');
      const top = submenu.getBoundingClientRect().top;
      shift = Math.min(
        Math.max(0, top + height - window.innerHeight),
        Math.max(0, top),
      );
      submenu.style.setProperty('--w3css-menu-submenu-shift', `${-shift}px`);
    }
    fitHeight(submenu, submenu.getBoundingClientRect().top, height);
  }

  /**
   * Places the open flyouts again, outermost first.
   *
   * @param {EventTarget} [scrolled]
   *   What scrolled, if anything. Only the flyouts inside it have moved. A
   *   scrolling submenu is left alone by its own scroll.
   */
  function placeOpenSubmenus(scrolled) {
    all(`.${ITEM_HOVER} > .w3css-menu__submenu, ${OPEN_SUBMENUS}`)
      .filter(function (submenu) {
        return (
          !scrolled || (submenu !== scrolled && scrolled.contains(submenu))
        );
      })
      .forEach(function (submenu) {
        placeSubmenu(submenu);
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
    } else {
      menus.forEach(function (menu) {
        slide(menu, false);
      });
      closeSubmenus(all(`${parents} li .w3css-menu__submenu`));
    }
    syncExpanded();
  }

  /**
   * Closes what Escape should close, and returns focus to what opened it.
   *
   * The innermost open submenu goes first, then the mobile or side menu.
   */
  function closeOnEscape() {
    const active = document.activeElement;
    let item = active ? active.closest(MENU_ITEM) : null;
    while (item) {
      const submenu = childSubmenu(item);
      if (
        submenu &&
        (submenu.classList.contains(SUBMENU_OPEN) ||
          item.classList.contains(ITEM_HOVER))
      ) {
        closeBranch(submenu);
        syncExpanded();
        if (submenu.previousElementSibling) {
          submenu.previousElementSibling.focus();
        }
        return;
      }
      item = item.parentElement.closest(MENU_ITEM);
    }

    // Focus is elsewhere: a submenu open under the pointer, or left open.
    const hovered = all(`.${ITEM_HOVER}`).pop();
    const pinned = all(OPEN_SUBMENUS).pop();
    if (hovered || pinned) {
      closeBranch(hovered ? childSubmenu(hovered) : pinned);
      syncExpanded();
      if (!hovered && pinned.previousElementSibling) {
        pinned.previousElementSibling.focus();
      }
      return;
    }

    const toggled = MENU_TOGGLES.find(function (pair) {
      const openMenus = all(pair[1]).filter(function (menu) {
        return !isHidden(menu);
      });
      return all(pair[0]).some(function (toggle) {
        return (
          !isHidden(toggle) &&
          openMenus.length &&
          (toggle === active ||
            openMenus.some(function (menu) {
              return menu.contains(active);
            }))
        );
      });
    });
    if (toggled) {
      toggleMenu(toggled[1]);
      document.querySelector(toggled[0]).focus();
      return;
    }

    const sideMenu = document.querySelector('.w3css-main-nav--vertical');
    const closer = document.querySelector('.w3css-main-nav__close');
    if (
      sideMenu &&
      closer &&
      !isHidden(sideMenu) &&
      sideMenu.contains(active)
    ) {
      closer.click();
    }
  }

  // The scroll padding last written to the root element.
  let scrollPadding = '';

  /**
   * Keeps a focused element clear of a bar pinned to the top (WCAG 2.4.11).
   *
   * Sets scroll-padding-top on the root to the bottom edge of the sticky or
   * fixed header or main menu, so the browser scrolls focus below it.
   */
  function updateScrollPadding() {
    let bottom = 0;
    [document.getElementById('w3css-header'), mainNavigation].forEach(
      function (bar) {
        if (!bar) {
          return;
        }
        const position = window.getComputedStyle(bar).position;
        if (position !== 'sticky' && position !== 'fixed') {
          return;
        }
        const box = bar.getBoundingClientRect();
        // Only a bar that is at the top edge covers what scrolls under it.
        if (box.top <= 1 && box.bottom > 0) {
          bottom = Math.max(bottom, Math.ceil(box.bottom));
        }
      },
    );
    const value = bottom ? `${bottom + 8}px` : '';
    if (value !== scrollPadding) {
      scrollPadding = value;
      document.documentElement.style.scrollPaddingTop = value;
    }
  }

  // Add flex position to the main menu at scroll.
  const scrollWindow = function () {
    if (mainNavigation) {
      if (window.scrollY > origOffsetY) {
        mainNavigation.classList.add('w3-sticky');
      } else {
        mainNavigation.classList.remove('w3-sticky');
      }
    }
    updateScrollPadding();
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
    if (all(`${container} > :is(div, main, aside)`).length > 1) {
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
      document.body.classList.toggle(`w3css-is-${name}-screen`, name === size);
    });
  }

  // Add and remove classes on window resize.
  const mediaSize = function () {
    // The same media queries as the CSS, so the screen class and the CSS
    // always agree, also when the visitor changes the browser text size.
    const isLarge = window.matchMedia('(min-width: 62rem)').matches;
    const isSmall = window.matchMedia('(max-width: 37.49875rem)').matches;
    const mainMenuChild = computedStyle(
      '.w3css-main-nav--horizontal',
      'background-color',
    );

    if (isLarge) {
      setScreenClass('large');
      // The inline display goes below, so the open state has to go with it.
      all(
        '.w3css-menu--responsive-h .w3css-menu__submenu, .w3css-main-nav--horizontal .w3css-menu__submenu',
      ).forEach(function (element) {
        element.classList.remove(SUBMENU_OPEN);
      });
      all('.w3css-menu__list, .w3css-menu__submenu').forEach(
        function (element) {
          element.classList.remove('w3-show');
        },
      );
      clearInlineStyles('.w3css-menu--responsive-h .w3css-menu__list');
      clearInlineStyles('.w3css-menu--responsive-h .w3css-menu__submenu');
      clearInlineStyles('.w3css-main-nav--horizontal .w3css-menu__list');
      clearInlineStyles('.w3css-main-nav--horizontal .w3css-menu__submenu');
      all('.w3css-main-nav--horizontal .w3css-menu__submenu').forEach(
        function (element) {
          setStyle(element, 'background-color', mainMenuChild);
        },
      );
      addFlexDisplay('.w3css-top-region__inner');
      addFlexDisplay('.w3css-main-region__inner > .w3-clear');
      addFlexDisplay('.w3css-bottom-region__inner');
      addFlexDisplay('.w3css-footer-region__inner');
    } else {
      // Remove the match height on medium/small screen.
      all(
        '.w3css-top-region__inner, .w3css-main-region__inner > .w3-clear, .w3css-bottom-region__inner, .w3css-footer-region__inner',
      ).forEach(function (element) {
        element.classList.remove('w3-f-display');
      });
    }

    if (!isLarge && !isSmall) {
      setScreenClass('medium');
    }

    if (isSmall) {
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
      closeBranch(submenu);
    } else {
      const list = trigger.parentElement && trigger.parentElement.parentElement;
      if (list) {
        closeSubmenus(
          Array.from(
            list.querySelectorAll(':scope li .w3css-menu__submenu'),
          ).filter(function (element) {
            return element !== submenu;
          }),
        );
      }
      if (submenu) {
        submenu.parentElement.classList.remove(ITEM_DISMISSED);
        submenu.classList.add(SUBMENU_OPEN);
        slide(submenu, true);
        placeSubmenu(submenu);
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
    let ancestor =
      element.parentElement && element.parentElement.closest(selector);
    while (ancestor) {
      depth++;
      ancestor =
        ancestor.parentElement && ancestor.parentElement.closest(selector);
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
   * Keeps a behavior name released in 2.x working under its 3.0 name.
   *
   * Deprecated in 3.0, removed in the next major. Sub-theme code that reads,
   * extends or replaces Drupal.behaviors.<oldName> reaches
   * Drupal.behaviors.<newName>. The alias is not enumerable, so
   * Drupal.attachBehaviors() runs the behavior once. Deleting the old name
   * removes only the alias; delete the new name to stop the behavior.
   *
   * @param {string} oldName
   *   The 2.x behavior name.
   * @param {string} newName
   *   The 3.0 behavior name.
   */
  function aliasBehavior(oldName, newName) {
    if (Object.prototype.hasOwnProperty.call(Drupal.behaviors, oldName)) {
      return;
    }
    Object.defineProperty(Drupal.behaviors, oldName, {
      configurable: true,
      get: () => Drupal.behaviors[newName],
      set: (value) => {
        Drupal.behaviors[newName] = value;
      },
    });
  }

  aliasBehavior('d8w3cssMenuDepth', 'w3cssMenuDepth');
  aliasBehavior('d8w3cssResponsiveMenu', 'w3cssMenuResponsive');
  aliasBehavior('d8w3cssMainNav', 'w3cssPageNav');
  aliasBehavior('d8w3cssTheme', 'w3cssPage');

  /**
   * Add depth classes to menu items.
   */
  Drupal.behaviors.w3cssMenuDepth = {
    attach(context) {
      // Add class to any UL/LI according to the depth.
      once('w3css-menu-depth-list', 'ul', context).forEach(function (element) {
        element.classList.add(
          `w3css-list-depth-${countAncestors(element, 'ul')}`,
        );
      });
      once('w3css-menu-depth-item', 'li', context).forEach(function (element) {
        element.classList.add(
          `w3css-menu__item--depth-${countAncestors(element, 'li')}`,
        );
      });
    },
  };

  /**
   * Responsive menu behavior.
   */
  Drupal.behaviors.w3cssMenuResponsive = {
    attach(context) {
      once(
        'w3css-menu-responsive-toggle',
        '.w3css-menu__responsive-toggle',
        context,
      ).forEach(function (element) {
        makeButton(element, Drupal.t('Toggle menu'));
        element.addEventListener('click', function () {
          toggleMenu('.w3css-menu--responsive-h .w3css-menu__list');
        });
      });

      once(
        'w3css-menu-link-toggle-vertical',
        '.w3css-menu__link-toggle--vertical',
        context,
      ).forEach(function (element) {
        element.addEventListener('click', function (event) {
          event.preventDefault();
          showHide(element);
        });
      });

      once(
        'w3css-menu-arrow-toggle',
        '.w3css-menu__arrow-toggle',
        context,
      ).forEach(function (element) {
        makeButton(element, submenuToggleLabel(element));
        element.addEventListener('click', function () {
          showHide(element);
        });
      });
    },
  };

  /**
   * Main navigation behavior.
   */
  Drupal.behaviors.w3cssPageNav = {
    attach(context) {
      // On click show/hide the vertical main menu.
      once('w3css-page-nav-close', '.w3css-main-nav__close', context).forEach(
        function (element) {
          // Templates copied into a sub-theme may still print a div here.
          if (element.tagName !== 'BUTTON') {
            makeButton(element, Drupal.t('Close side menu'));
          }
          element.addEventListener('click', function () {
            const opener = document.querySelector('.w3css-main-nav__open');
            document.querySelector('.w3css-main-nav--vertical').style.display =
              'none';
            closeSubmenus(
              all(
                '.w3css-main-nav--vertical .w3css-menu__list li .w3css-menu__submenu',
              ),
            );
            syncExpanded();
            if (opener) {
              opener.setAttribute('aria-expanded', 'false');
              opener.focus();
            }
          });
        },
      );

      once('w3css-page-nav-open', '.w3css-main-nav__open', context).forEach(
        function (element) {
          if (element.tagName !== 'BUTTON') {
            makeButton(element, Drupal.t('Open side menu'));
          }
          element.addEventListener('click', function () {
            const closer = document.querySelector('.w3css-main-nav__close');
            document.querySelector('.w3css-main-nav--vertical').style.display =
              'block';
            element.setAttribute('aria-expanded', 'true');
            if (closer) {
              closer.focus();
            }
          });
        },
      );

      // Show the mobile menu on click horizontal.
      once(
        'w3css-page-nav-toggle',
        '.w3css-mobile-nav__toggle',
        context,
      ).forEach(function (element) {
        makeButton(element, Drupal.t('Toggle menu'));
        element.addEventListener('click', function () {
          toggleMenu('.w3css-main-nav--horizontal .w3css-menu__list');
        });
      });

      // On click expand the dropdown menu for small device.
      once(
        'w3css-menu-link-toggle',
        '.w3css-menu__link-toggle',
        context,
      ).forEach(function (element) {
        element.addEventListener('click', function (event) {
          event.preventDefault();
          showHide(element);
        });
      });
    },
  };

  /**
   * Hover state, staying on screen, and closing the open submenus.
   */
  Drupal.behaviors.w3cssMenuState = {
    attach(context) {
      once('w3css-menu-hover', MENU_ITEM, context).forEach(function (item) {
        item.addEventListener('mouseenter', function () {
          const submenu = childSubmenu(item);
          if (submenu && isFlyout(submenu)) {
            item.classList.add(ITEM_HOVER);
            // A submenu left open on another item would sit under this one.
            Array.from(item.parentElement.children).forEach(function (sibling) {
              const other = sibling !== item && childSubmenu(sibling);
              if (other && other.classList.contains(SUBMENU_OPEN)) {
                closeBranch(other);
              }
            });
            placeSubmenu(submenu);
            syncExpanded();
          }
        });
        item.addEventListener('mouseleave', function () {
          item.classList.remove(ITEM_HOVER, ITEM_DISMISSED);
          syncExpanded();
        });
      });

      // Tabbing out of an open flyout closes it. In the flow, a submenu stays.
      once('w3css-menu-focusout', '.w3css-menu__list', context).forEach(
        function (list) {
          list.addEventListener('focusout', function (event) {
            if (event.relatedTarget) {
              closeSubmenus(
                Array.from(list.querySelectorAll(OPEN_SUBMENUS)).filter(
                  function (submenu) {
                    return (
                      isFlyout(submenu) &&
                      !submenu.parentElement.contains(event.relatedTarget)
                    );
                  },
                ),
              );
              syncExpanded();
            }
          });
        },
      );

      once('w3css-menu-document', 'html').forEach(function () {
        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            closeOnEscape();
          }
        });
        document.addEventListener('click', function (event) {
          if (!event.target.closest('.w3css-menu__list')) {
            closeSubmenus(all(OPEN_SUBMENUS).filter(isFlyout));
            syncExpanded();
          }
        });
        // Scroll does not bubble, and a flyout can sit in a scrolling submenu.
        document.addEventListener(
          'scroll',
          function (event) {
            placeOpenSubmenus(event.target);
          },
          { capture: true, passive: true },
        );
        window.addEventListener('resize', function () {
          placeOpenSubmenus();
        });
      });
    },
  };

  /**
   * Search forms: toolbar search, footer search form and the search page.
   */
  Drupal.behaviors.w3cssSearch = {
    attach(context) {
      // In case the admin toolbar search installed.
      once(
        'w3css-search-toolbar',
        '#admin-toolbar-search-tab > div',
        context,
      ).forEach(function (element) {
        element.classList.remove('w3-section');
      });

      // Change the form color to match the footer color. The colors are read
      // only when a new form is found, so an Ajax attach does not force a
      // style recalculation for nothing.
      const footerForms = once(
        'w3css-search-footer-form',
        '.w3css-footer-menu form',
        context,
      );
      if (footerForms.length) {
        const footerFormBg = computedStyle(
          '.w3css-footer-menu',
          'background-color',
        );
        const footerFormTxt = computedStyle('.w3css-footer-menu', 'color');
        footerForms.forEach(function (element) {
          setStyle(element, 'background-color', footerFormBg);
          setStyle(element, 'color', footerFormTxt);
        });
      }

      // Add classes to search page.
      addClasses(
        once('w3css-search-advanced', '.search-form .search-advanced', context),
        'w3-padding-large w3-border w3-bar w3-left-align w3-margin-bottom',
      );
      addClasses(
        once(
          'w3css-search-summary',
          '.search-form .search-advanced > summary',
          context,
        ),
        'w3-button w3-bar w3-left-align',
      );
      addClasses(
        once('w3css-search-help', '.search-form .search-help-link', context),
        'w3-button w3-right w3-border w3-margin-top',
      );
    },
  };

  /**
   * General page behavior.
   */
  Drupal.behaviors.w3cssPage = {
    attach(context) {
      addClasses(
        once(
          'w3css-table-tabledrag-toggle',
          '.tabledrag-toggle-weight-wrapper button',
          context,
        ),
        'w3-button',
      );

      // Theme settings. The form decides which sections start open, and the
      // settings form script shows one section at a time, so nothing is
      // closed here.
      const settingsDetails = once(
        'w3css-settings-details',
        '#system-theme-settings details',
        context,
      );
      addClasses(
        settingsDetails,
        'w3-border w3-bar w3-left-align w3-margin-bottom',
      );
      addClasses(
        once(
          'w3css-settings-summary',
          '#system-theme-settings details > summary',
          context,
        ),
        'w3-button w3-bar w3-left-align',
      );
      addClasses(
        once(
          'w3css-settings-wrapper',
          '#system-theme-settings details > .details-wrapper',
          context,
        ),
        'w3-padding w3-left-align',
      );

      // Disable top margin if breadcrumb exist.
      if (document.querySelector('.w3css-breadcrumb')) {
        addClasses(
          once('w3css-page-main', '.w3css-page__main', context),
          'w3css-page__main--breadcrumb-found',
        );
      }

      once('w3css-image-link', 'a > .w3-image', context).forEach(
        function (element) {
          element.parentElement.classList.add('w3css-image__link');
        },
      );

      mediaSize();
      once('w3css-page-window-listeners', 'html').forEach(function () {
        window.addEventListener('resize', mediaSize);
        window.addEventListener('resize', updateScrollPadding);
        window.addEventListener('scroll', scrollWindow);
        updateScrollPadding();
      });
    },
  };
})(Drupal, once);
