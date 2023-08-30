/**
 * @file
 * Defines Javascript behaviors for the W3CSS Theme.
 * https://www.drupal.org/node/3158256
 */
((Drupal, once) => {

'use strict';

  /**
   * d8w3css helper functions.
   *
   * @namespace
   */
  Drupal.d8w3css = {};


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
  Drupal.d8w3css.slideUp = slideUp;

  const removeStyles = (target) => {
    const stylesToRemove = ['height', 'paddingTop', 'paddingBottom', 'marginTop', 'marginBottom', 'overflow', 'transitionDuration', 'transitionProperty'];
    stylesToRemove.forEach(st => target.style.removeProperty(st));
  }

  Drupal.d8w3css.removeStyles = removeStyles;

  const slideDown = (target, divDis = 'block', duration = 600) => {
    target.style.removeProperty('display');
    let display = window.getComputedStyle(target)
      .display;

    if (display === 'none') display = divDis;

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

    ['paddingTop', 'paddingBottom', 'marginTop', 'marginBottom'].forEach(
      property => {
        target.style.removeProperty(property);
      });

    setTimeout(() => {
      ['height', 'overflow', 'transitionDuration', 'transitionProperty'].forEach(
        property => target.style.removeProperty(property));
    }, duration);
  }
  Drupal.d8w3css.slideDown = slideDown;

  const slideToggle = (target, duration = 500) => {
    if (!target.classList.contains('show')) {
      return slideDown(target, duration);
    } else {
      return slideUp(target, duration);
    }
  }
  Drupal.d8w3css.slideToggle = slideToggle;



    // Get current width
  const getCurrentWidth = () => window.innerWidth || document.documentElement
    .clientWidth || document.body.clientWidth;

  const addClassAccordingToDepth = (element, depth) => {

    element.classList.add(`ul-${depth}`);
    Array.from(element.children)
      .forEach(child => child.classList.add(`li-${depth}`));
  };

  const calculateDepth = (element) => {
    let depth = 1;
    let parent = element.parentElement;

    while (parent) {
      if (parent.tagName.toLowerCase() === 'ul') {
        depth++;
      }
      parent = parent.parentElement;
    }
    return depth;
  };

  Drupal.d8w3css.calculateDepth = calculateDepth;

  Drupal.behaviors.d8w3cssMenuDepth = {
    attach: (settings) => {
      Array.from(document.querySelectorAll('.d-inner ul'))
        .forEach((element) => {
          const depth = calculateDepth(element);
          addClassAccordingToDepth(element, depth);
        });
    }
  };





})(Drupal, once);
