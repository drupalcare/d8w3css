/**
 * @file
 * Defines Javascript behaviors for the W3CSS Theme.
 * https://www.drupal.org/node/3158256
 */
((Drupal, once) => {

    'use strict';

    let isClicked = false;

    let uLm = '';
    let currentWidth = '';
    const navMenus = [...document.querySelectorAll('nav ul')] ?? null;

    const menuBars = [...document.querySelectorAll('.navigation__menubar')] ?? null;
    const subMenus = [...document.querySelectorAll('.navigation__menubar ul')] ?? null;
    const svgIcons = [...document.querySelectorAll('.navigation__menubar .toggler-icon svg')] ?? null;

    // Get current width
    const getCurrentWidth = () => window.innerWidth || document.documentElement
        .clientWidth || document.body.clientWidth;

    // Add parent background to ul child.
    const getParentBg = (el) => {
        const closestParent = el.closest('.page-wrapper>div') ?? el.closest(
            '.page-wrapper>header');
        if (closestParent) {
            let parentBg = window.getComputedStyle(closestParent)
                .backgroundColor;
            return parentBg;
        }
    }

    subMenus.forEach(el => {
        el.style.backgroundColor = getParentBg(el);
    });

    // Used for fixed menu.
    let origOffsetY;
    const mainNavigation = document.querySelector('#main-navigation-h');
    if (mainNavigation && mainNavigation.querySelector('.navigation__menubar')) {
        origOffsetY = mainNavigation.offsetTop;
    }

    // Add flex position to the main menu at scroll.
    const scrollWindow = () => {
        if (mainNavigation) {
            mainNavigation.classList.toggle('w3-sticky', window.scrollY >
                origOffsetY);
        }
    };

    const showMenuBarOnResize = (menuBars) => {
        if (currentWidth <= 992) {
        console.log(currentWidth);
            let isOpen = document.querySelector('.mobile-nav') ?? null;

            if (isOpen) {
                menuBars.forEach(el => {
             console.log(el);
                    if (isOpen.classList.contains('show')) {
                        Drupal.d8w3css.slideDown(el, 100);
                        console.log('yes')
                    }
                });

            }

        }

    }

    const resetNavs = (navMenu, svgIcons) => {

        navMenu.forEach(el => Drupal.d8w3css.slideUp(el, 400));

        svgIcons.forEach(el => el.style.removeProperty('transform'));

        setTimeout(() => {
            navMenu.forEach(el => el.style.removeProperty('transform'));
        }, 400);

    };

    // Used for top navigation only on horizontal.
    const revertIcon = (navId) => {
        let svgIcons = document.querySelectorAll(`#${navId} .toggler-icon svg`);
        svgIcons.forEach((svgIcon) => {
            svgIcon.style.removeProperty('transform');
        });
    }

    const hideChildren = (childElement) => {
        Drupal.d8w3css.slideUp(childElement, 300);
    }

    const menubarToggler = menuBar => {
        const mb = document.querySelector(`#${menuBar} .menubar-navigation`);
        const childrenOnly = document.querySelectorAll(`#${menuBar} .menubar-navigation ul`);
        console.log(menuBar);

        if (!mb.classList.contains('show')) {
            childrenOnly?.forEach((ulChild) => {
                hideChildren(ulChild);
                revertIcon(menuBar);
            });
            Drupal.d8w3css.slideDown(mb, 'flex');
        } else {
            childrenOnly?.forEach((ulChild) => {
                hideChildren(ulChild);
                revertIcon(menuBar);
            });

            Drupal.d8w3css.slideUp(mb, 400);
        }

    };

    const menuToggler = (btn, uLm, t) => {

        const {
            id: parentNav
        } = btn.closest('nav');
        const rotated = btn.querySelector('.toggler-icon svg');
        const ulDepth = Drupal.d8w3css.calculateDepth(uLm);

        const parentChildren = document.querySelector(`#${parentNav} ul`);
        const childrenOnly = document.querySelectorAll(`#${parentNav} .sub__menu`);

        if (btn.parentElement.classList.contains('navigation__menubar-item')) {

            if (!uLm.classList.contains('show')) {
                childrenOnly?.forEach((ulChild) => {
                    if (ulChild !== uLm) {
                        hideChildren(ulChild);
                        revertIcon(parentNav);
                    }
                });

                rotated.style.transform = 'rotate(180deg)';
                if (currentWidth > 992) {
                    const btnHeight = btn.offsetHeight - 1;
                    uLm.style.transform = `translateY(${btnHeight}px)`;
                }
                btn.setAttribute('aria-expanded', 'true');
                btn.setAttribute('aria-hidden', 'false');

                Drupal.d8w3css.slideDown(uLm);
            } else {

                childrenOnly?.forEach((ulChild) => {
                    hideChildren(ulChild);
                    revertIcon(parentNav);
                });
                Drupal.d8w3css.slideUp(uLm, 400);

                rotated.style.removeProperty('transform');
                btn.setAttribute('aria-expanded', 'false');
                btn.setAttribute('aria-hidden', 'true');

            }

        } else {
            // Second level on horizontal menu.
            const btnSibling = btn.closest('ul');

            const allMenus = Array.from(btnSibling.querySelectorAll('ul'));
            const allTogglers = Array.from(btnSibling.querySelectorAll('.dropdown-toggler svg'));

            if (!uLm.classList.contains('show')) {
                // close all opened sibling menu
                allMenus.forEach((ulChild) => {
                    if (ulChild !== uLm) {
                        hideChildren(ulChild);
                    }
                });

                // revert all rotated icons
                allTogglers.forEach((tIcon) => {
                    if (tIcon !== btn) {
                        tIcon.style.removeProperty('transform');
                    }
                });
                // rotate 90 only if it is bigger than 992px
                let arrowDirection = currentWidth > 992 ? 'rotate(-90deg)' : 'rotate(180deg)';

                rotated.style.transform = arrowDirection;

                btn.setAttribute('aria-expanded', 'true');
                btn.setAttribute('aria-hidden', 'false');
                Drupal.d8w3css.slideDown(uLm);

            } else {
                Drupal.d8w3css.slideUp(uLm, 400);

                rotated.style.removeProperty('transform');
                btn.setAttribute('aria-expanded', 'false');
                btn.setAttribute('aria-hidden', 'true');

            }

        } //navigation__menubar-item
        // } // currentWidth

    } // menuToggler

    // click anywhere to close the nav
    function removeMenubarsStyle(menuBars) {
        menuBars.forEach((el) => {
            el.removeAttribute('style');
            console.log('removed');
        });
    }

    // Common function to handle click
    const handleClick = (callback) => {
        if (!isClicked) {
            isClicked = true;
            callback();
            setTimeout(() => {
                isClicked = false;
            }, 500); // 500ms delay
        }
    }

    // For document click
    document.addEventListener('click', (event) => {
        handleClick(() => {
            const navMenu = '.navigation__menubar';
            if (!event.target.closest(navMenu)) {
                resetNavs(subMenus, svgIcons);
            }
        });
    });

    // For all menus
    const handleMenuClick = (btn, uLm, t) => {
        handleClick(() => {
            menuToggler(btn, uLm, t);
        });
    }

    document.querySelectorAll('.dropdown-toggler')
        .forEach((btn) => {
            btn.addEventListener('click', (event) => {
                const uLm = btn.nextElementSibling;
                const t = event.target;
                handleMenuClick(btn, uLm, t);
            });
        });

    // Mobile nav < 992 px
    const handleMobileNavClick = (clickedItem) => {
        handleClick(() => {
            menubarToggler(clickedItem);
        });
    }
    // Mobile nav button
    const mobileNavButtons = document.querySelectorAll('.mobile-nav');
    mobileNavButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('show');
            let btnChild = btn.children[0];
            let isExpanded = btnChild.getAttribute('aria-expanded') === 'true';
            btnChild.setAttribute('aria-expanded', String(!isExpanded));
            const {
                id: navi
            } = btn.nextElementSibling;
            handleMobileNavClick(navi);
        });
    });

    Drupal.behaviors.menuAction = {
        attach: function(settings) {

            currentWidth = getCurrentWidth();
            window.addEventListener('scroll', scrollWindow);
            window.addEventListener('resize', () => {
                resetNavs(subMenus, svgIcons);
                currentWidth = getCurrentWidth();

                showMenuBarOnResize(navMenus);
                removeMenubarsStyle(menuBars); // At the bottom.
            });

        }
    };

})(Drupal, once);
