/**
 * @file
 * Defines Javascript behaviors for the W3CSS Theme.
 */
(function(Drupal, $, once) {
  'use strict';
  // Cache the selectors
  const socialMediaIcons = document.querySelectorAll('.social-media > a > i');
  const adminToolbarSearchTab = document.querySelector('#admin-toolbar-search-tab > div');
  const mainMenuBg = window.getComputedStyle(document.querySelector('.main-navigation-wrapper')).backgroundColor;
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
  // In case the main menu not printed.
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
      document.querySelectorAll(selector).forEach(el => {
        el.classList.remove('w3-show');
        el.removeAttribute('style');
      });
    };
    const addDisplayClass = (selector) => {
      if (document.querySelectorAll(selector + ' > div').length > 1) {
        document.querySelector(selector).classList.add('w3-f-display');
      }
    };
    const removeDisplayClass = (selectors) => {
      selectors.forEach(selector => {
        document.querySelectorAll(selector).forEach(el => {
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
      document.querySelectorAll('#main-navigation-h .ul-child').forEach(el => {
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
    Array.from(element.children).forEach(child => child.classList.add(`li-${depth}`));
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
  Drupal.behaviors.d8w3cssMenuDepth = {
    attach: (settings) => {
      Array.from(document.querySelectorAll('body ul')).forEach((element) => {
        const depth = calculateDepth(element);
        addClassAccordingToDepth(element, depth);
      });
    }
  };

  function show_hide($this) {
    if ($this.next().hasClass('show')) {
      $this.next().removeClass('show');
      $this.next().slideUp(350);
    } else {
      $this.parent().parent().find('li .ul-child').removeClass('show');
      $this.parent().parent().find('li .ul-child').slideUp(350);
      $this.next().toggleClass('show');
      $this.next().slideToggle(350);
    }
  }
  Drupal.behaviors.d8w3cssResponsiveMenu = {
    attach: function(context, settings) {
      // $(context)
      //   .find('.mobile-nav-responsive')
      //   .once('.mobile-nav-responsive')
      $(once('.mobile-nav-responsive', '.mobile-nav-responsive', context)).on('click', function() {
        if ($('.ul-responsive-h .ul-parent').is(':hidden')) {
          $('.ul-responsive-h .ul-parent').slideDown(350);
        } else {
          $('.ul-responsive-h .ul-parent').slideUp(350);
          if ($('.ul-responsive-h .ul-parent li .ul-child')) {
            $('.ul-responsive-h .ul-parent li .ul-child').removeClass('show');
            $('.ul-responsive-h .ul-parent li .ul-child').slideUp(350);
          }
        }
      });
      $(once('body', '.tMenu-v', context)).on('click', function(e) {
        e.preventDefault();
        let $this = $(this);
        show_hide($this);
      });
      $(once('body', '.clickable-dropdown-menu-fa-down', context)).on('click', function() {
        let $this = $(this);
        show_hide($this);
      });
      $(once('body', '.clickable-dropdown-menu-fa-down-h', context)).on('click', function() {
        let $this = $(this);
        show_hide($this);
      });
      $(once('body', '.clickable-dropdown-menu-fa-down-v', context)).on('click', function() {
        let $this = $(this);
        show_hide($this);
      });
    }
  };
  Drupal.behaviors.d8w3cssMainNav = {
    attach: function(context, settings) {
      // Change the sub menu color as the main menu.
      // On click show/hide the vertical main menu.
      $(once('#main-navigation-v', '#close-nav', context)).on('click', function() {
        document.getElementById('main-navigation-v').style.display = 'none';
        if ($('#main-navigation-v .ul-parent li .ul-child')) {
          $('#main-navigation-v .ul-parent li .ul-child').removeClass('show');
          $('#main-navigation-v .ul-parent li .ul-child').slideUp(350);
        }
      });
      $(once('body', '#open-nav-inner', context)).on('click', function() {
        document.getElementById('main-navigation-v').style.display = 'block';
      });
      // Show the mobile menu on click horizontal.
      $(once('#main-navigation-h', '.mobile-nav', context)).on('click', function() {
        if ($('#main-navigation-h .ul-parent').is(':hidden')) {
          $('#main-navigation-h .ul-parent').slideDown(350);
        } else {
          $('#main-navigation-h .ul-parent').slideUp(350);
          if ($('#main-navigation-h .ul-parent li .ul-child')) {
            $('#main-navigation-h .ul-parent li .ul-child').removeClass('show');
            $('#main-navigation-h .ul-parent li .ul-child').slideUp(350);
          }
        }
      });
      // On click expand the dropdown menu for small device.
      $(once('body', '.tMenu', context)).on('click', function(e) {
        e.preventDefault();
        let currentWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        let $this = $(this);
        if ((currentWidth <= 992) || ($('#main-navigation-v').css('display') === 'block')) {
          show_hide($this);
        }
      });
    }
  };
  Drupal.behaviors.d8w3cssTheme = {
    attach: function(context, settings) {
      // Change the form color to match the footer color.
      const footerFormBg = window.getComputedStyle(document.querySelector('#footer-menu')).backgroundColor;
      const footerFormTxt = window.getComputedStyle(document.querySelector('#footer-menu')).color;
      let footerMenu = document.querySelector('#footer-menu');
      let footerMenuForm = document.querySelector('#footer-menu form');
      if (footerMenu && footerMenuForm) {
        footerMenuForm.style.background = footerFormBg;
        footerMenuForm.style.color = footerFormTxt;
      }
      // apply background and color to opened dialog.
      let layoutBuilderBox = function() {
        if (document.querySelector('.ui-dialog') !== null && document.querySelector('.ui-dialog').style.display !== 'none') {
          const pageTitle = document.querySelector('#page-title');
          const pageTitleBg = window.getComputedStyle(pageTitle).getPropertyValue('background-color');
          const pageTitleH1 = document.querySelector('#page-title h1');
          const pageTitleTxt = window.getComputedStyle(pageTitleH1).getPropertyValue('color');
          const mainContainer = document.querySelector('#main-container');
          const mainContainerBg = window.getComputedStyle(mainContainer).getPropertyValue('background-color');
          const mainContainerP = document.querySelector('#main-container p');
          const mainContainerTxt = window.getComputedStyle(mainContainerP).getPropertyValue('color');
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
      }

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
})(Drupal, jQuery, once);
