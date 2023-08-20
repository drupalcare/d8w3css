/**
 * @file
 * Defines Javascript behaviors for the W3CSS Theme.
 * https://www.drupal.org/node/3158256
 */
((Drupal, once) => {
  'use strict';

  // Slide up and down
  let cssStyles = [
    ['overflow', 'hidden'],
    ['height', '0'],
    ['paddingTop', '0'],
    ['paddingBottom', '0'],
    ['marginTop', '0'],
    ['marginBottom', '0']
  ];

  var slideUp = (target, duration = 500) => {

    target.style.transitionProperty = 'height, margin, padding';
    target.style.transitionDuration = `${duration}ms`;
    target.style.boxSizing = 'border-box';
    target.style.height = `${target.offsetHeight}px`;
    target.offsetHeight;
    target.classList.remove('show');

    cssStyles.forEach(cssStyle => {
      target.style[cssStyle[0]] = cssStyle[1];
    });

    window.setTimeout(() => {
      target.style.display = 'none';
      removeStyles(target);
    }, duration);
  }

  function removeStyles(target) {
    const stylesToRemove = ['height', 'padding-top', 'padding-bottom', 'margin-top', 'margin-bottom', 'overflow', 'transition-duration', 'transition-property'];
    stylesToRemove.forEach(style => target.style.removeProperty(style));
  }

  var slideDown = (target, duration = 500) => {
    target.style.removeProperty('display');
    let display = window.getComputedStyle(target)
      .display;

    if (display === 'none')
      display = 'block';

    target.style.display = display;
    let height = target.offsetHeight;

    cssStyles.forEach(cssStyle => {
      target.style[cssStyle[0]] = cssStyle[1];
    });

    target.offsetHeight;
    target.style.boxSizing = 'border-box';
    target.style.transitionProperty = "height, margin, padding";
    target.style.transitionDuration = `${duration}ms`;
    target.style.height = `${height}px`;
    target.classList.add('show');

    ['padding-top', 'padding-bottom', 'margin-top', 'margin-bottom'].forEach(property => {
      target.style.removeProperty(property);
    });

    window.setTimeout(() => {
      ['height', 'overflow', 'transition-duration', 'transition-property'].forEach(property => target.style.removeProperty(property));
    }, duration);

  }

  var slideToggle = (target, duration = 500) => {
    if (!target.classList.contains('show')) {
      return slideDown(target, duration);
    } else {
      return slideUp(target, duration);
    }
  }

  // Cache the selectors
  var socialMediaIcons = document.querySelectorAll('.social-media > a > i');
  var adminToolbarSearchTab = document.querySelector('#admin-toolbar-search-tab > div');

  var mainMenuBg, displayVertical;

  var mainNavWrapper = document.querySelector('.main-navigation-wrapper');
  if (mainNavWrapper) {
    mainMenuBg = window.getComputedStyle(mainNavWrapper)
      .backgroundColor;
  }

  var mainNavVertical = document.querySelector('#main-navigation-v');
  if (mainNavVertical) {
    displayVertical = window.getComputedStyle(mainNavVertical)
      .display;
  }

  // Get current widht
  function getCurrentWidth() {
    return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  }

  // Remove Fa class from social links.
  if (socialMediaIcons.length > 0) {
    socialMediaIcons.forEach(icon => {
      if (icon.classList.contains('fa')) {
        icon.classList.remove('fa');
      }
    });
  }

  // In case the admin toolbar search installed.
  if (adminToolbarSearchTab > 0) {
    adminToolbarSearchTab.classList.remove('w3-section');
  }

  // Used for fixed menu.
  let origOffsetY;
  let mainNavigation = document.querySelector('#main-navigation-h');
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
  let mediaSize = function() {
    const currentWidth = getCurrentWidth();
    const containers = ['.top-container-inner', '.main-container-inner > .w3-clear', '.bottom-container-inner', '.footer-container-inner'];
    const bodyTag = document.body;

    const removeStyles = (selector) => {
      document.querySelectorAll(selector)
        .forEach(el => {
          el.classList.remove('w3-show');
          el.removeAttribute('style');
        });
    };

    const addDisplayClass = (selector) => {
      if (document.querySelectorAll(selector + ' > div')
        .length > 1) {
        document.querySelector(selector)
          .classList.add('w3-f-display');
      }
    };

    const removeDisplayClass = (selectors) => {
      selectors.forEach(selector => {
        document.querySelectorAll(selector)
          .forEach(el => {
            el.classList.remove('w3-f-display');
          });
      });
    };

    if (currentWidth >= 993) {

      bodyTag.classList.add('large-screen');
      bodyTag.classList.remove('small-screen', 'medium-screen');
      removeStyles('.ul-parent');
      removeStyles('.ul-child');
      removeStyles('.ul-responsive-h .ul-parent');
      removeStyles('.ul-responsive-h .ul-child');
      removeStyles('#main-navigation-h .ul-parent');

      document.querySelectorAll('#main-navigation-h .ul-child')
        .forEach(el => {
          el.style.backgroundColor = mainMenuBg;
        });

      containers.forEach(addDisplayClass);

    } else {
      removeDisplayClass(containers);
    }

    if ((currentWidth >= 601) && (currentWidth <= 992)) {
      bodyTag.classList.add('medium-screen');
      bodyTag.classList.remove('large-screen', 'small-screen');
    }

    if (currentWidth <= 600) {
      bodyTag.classList.add('small-screen');
      bodyTag.classList.remove('large-screen', 'medium-screen');
    }

  };
  // Add class to any UL/LI according to the depth.

  const addClassAccordingToDepth = (element, depth) => {

    element.classList.add(`ul-${depth}`);
    Array.from(element.children)
      .forEach(child => child.classList.add(`li-${depth}`));
  };

  const calculateDepth = (element) => {
    let depth = 0;
    let parent = element.parentElement;

    while (parent) {
      if (parent.tagName.toLowerCase() === 'ul') {
        depth++;
      }
      parent = parent.parentElement;
    }
    return depth;
  };

  function hideChildren(childElement) {
    // childElement.classList.remove('show');
    // childElement.style.display = 'none';
    slideUp(childElement);
  }

  function hideParents(ulParent, ulChildren = '') {

    if (!ulParent.classList.contains('show')) {
      slideDown(ulParent);
    } else {
      slideUp(ulParent);
      if (ulChildren) {
        ulChildren.forEach(function(ulChild) {
          hideChildren(ulChild);
        });
      }
    }

  }

  const mainSiteNavH = document.querySelector('#main-navigation-h .mobile-nav');
  if (mainSiteNavH) {
    mainSiteNavH.addEventListener('click', () => {

      const ulParent = document.querySelector('#main-navigation-h .ul-parent');
      const ulChildren = document.querySelectorAll('#main-navigation-h .ul-parent .ul-child');
      hideParents(ulParent, ulChildren);
    });
  }

  const ulResposiveH = document.querySelector('.ul-responsive-h .mobile-nav-responsive');
  if (ulResposiveH) {
    ulResposiveH.addEventListener('click', () => {

      let ulParent = document.querySelector('.ul-responsive-h .ul-parent');
      let ulChildren = document.querySelectorAll('.ul-responsive-h .ul-parent .ul-child');
      hideParents(ulParent, ulChildren);
    });
  }

  let closeNav = document.querySelector('#main-navigation-v #close-nav');
  if (closeNav) {
    closeNav.addEventListener('click', function() {

      document.getElementById('main-navigation-v')
        .style.display = 'none';
      let childElements = document.querySelectorAll('#main-navigation-v .ul-parent li .ul-child');

      if (childElements) {
        childElements.forEach(function(childElement) {
          hideChildren(childElement);
        });
      }

    });
  }

  let openNav = document.querySelector('#open-nav-inner');
  if (openNav) {
    openNav.addEventListener('click', function() {

      document.getElementById('main-navigation-v')
        .style.display = 'block';
      displayVertical = window.getComputedStyle(document.querySelector('#main-navigation-v'))
        .display;
    });
  }

  const ulToggler = (element) => {
    let getDepth = calculateDepth(element);
    let menuUl = element.parentNode.querySelector('.ul-child.ul-' + getDepth);

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
    '.tMenu',
    '.tMenu-v',
    '.clickable-dropdown-menu-fa-down',
    '.clickable-dropdown-menu-fa-down-v',
    '.clickable-dropdown-menu-fa-down-h'
  ];

  selectors.forEach(selector => {
    if (document.querySelector(selector)) {
      addClickListener(selector);
    }
  });

  Drupal.behaviors.d8w3cssMenuDepth = {
    attach: (settings) => {
      Array.from(document.querySelectorAll('body ul'))
        .forEach((element) => {
          const depth = calculateDepth(element);
          addClassAccordingToDepth(element, depth);
        });
    }
  };

  Drupal.behaviors.d8w3cssTheme = {
    attach: function(context, settings) {
      // Change the form color to match the footer color.
      const footerFormBg = window.getComputedStyle(document.querySelector('#footer-menu'))
        .backgroundColor;
      const footerFormTxt = window.getComputedStyle(document.querySelector('#footer-menu'))
        .color;

      let footerMenu = document.querySelector('#footer-menu');
      let footerMenuForm = document.querySelector('#footer-menu form');

      if (footerMenu && footerMenuForm) {
        footerMenuForm.style.background = footerFormBg;
        footerMenuForm.style.color = footerFormTxt;
      }

      // apply background and color to opened dialog.
      let layoutBuilderBox = function() {
        if (document.querySelector('.ui-dialog') !== null && document.querySelector('.ui-dialog')
          .style.display !== 'none') {

          const pageTitle = document.querySelector('#page-title');
          const pageTitleBg = window.getComputedStyle(pageTitle)
            .getPropertyValue('background-color');

          const pageTitleH1 = document.querySelector('#page-title h1');
          const pageTitleTxt = window.getComputedStyle(pageTitleH1)
            .getPropertyValue('color');

          const mainContainer = document.querySelector('#main-container');
          const mainContainerBg = window.getComputedStyle(mainContainer)
            .getPropertyValue('background-color');

          const mainContainerP = document.querySelector('#main-container p');
          const mainContainerTxt = window.getComputedStyle(mainContainerP)
            .getPropertyValue('color');

          let dialogTitlebar = document.querySelector('.ui-dialog .ui-dialog-titlebar');

          if (dialogTitlebar) {
            dialogTitlebar.style.background = pageTitleBg;
            dialogTitlebar.style.color = pageTitleTxt;
          }

          let body = document.querySelector('body .ui-dialog');

          if (body) {
            body.style.background = mainContainerBg;
            body.style.color = mainContainerTxt;
          }

          let dialogContent = document.querySelector('.ui-dialog .ui-dialog-content');

          if (dialogContent) {
            dialogContent.style.background = mainContainerBg;
            dialogContent.style.color = mainContainerTxt;
          }

          let dialogButtonpane = document.querySelector('.ui-dialog .ui-dialog-buttonpane');

          if (dialogButtonpane) {
            dialogButtonpane.style.background = footerFormBg;
            dialogButtonpane.style.color = footerFormTxt;
          }

        }
      } // End of layoutBuilderBox

      function addClasses(selector, childSelector, classes, context) {

        const elements = context.querySelectorAll(`${selector} ${childSelector}`);
        elements.forEach(element => {
          element.classList.add(...classes.split(' '));
        });
      }

      // Add classes to search page.
      addClasses('.search-form', '.search-advanced', 'w3-padding-large w3-border w3-bar w3-left-align w3-margin-bottom', document);
      addClasses('.search-form', '.search-advanced > summary', 'w3-button w3-bar w3-left-align', document);
      addClasses('.search-form', '.search-help-link', 'w3-button w3-right w3-border w3-margin-top', document);
      addClasses('.tabledrag-toggle-weight-wrapper', 'button', 'w3-button', document);

      // Theme settings.
      addClasses('#system-theme-settings', 'details', 'w3-border w3-bar w3-left-align w3-margin-bottom', document);
      addClasses('#system-theme-settings', 'details > summary', 'w3-button w3-bar w3-left-align', document);
      addClasses('#system-theme-settings', 'details > .details-wrapper', 'w3-padding w3-left-align', document);
      // Remove attribute 'open'

      const detailsElements = document.querySelectorAll('#system-theme-settings details');
      detailsElements.forEach(element => {
        element.removeAttribute('open');
      });

      // Disable top margin if breadcrumb exist.
      let breadcrumb = document.querySelector('nav.breadcrumb');
      let mainBox = document.querySelectorAll('body .main-box');
      let w3Image = document.querySelectorAll('body a > .w3-image');

      if (breadcrumb && mainBox) {
        mainBox.forEach(function(element) {
          element.classList.add('breadcrumb-found');
        });
      }

      w3Image.forEach(function(image) {
        image.parentElement.classList.add('d8-has-image');
      });

      mediaSize();
      getCurrentWidth();
      window.addEventListener('resize', mediaSize, getCurrentWidth);
      window.addEventListener('scroll', scrollWindow);

    }
  };

})(Drupal, once);
