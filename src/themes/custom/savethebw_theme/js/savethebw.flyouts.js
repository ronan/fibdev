/* global jQuery, document */
import hoverintent from 'hoverintent';
import includes from 'lodash/includes';
import map from 'lodash/fp/map';

(($, Drupal) => {
  /* global Drupal */
  /* eslint-disable no-param-reassign */
  Drupal.behaviors.savethebwFlyouts = {
    attach: () => {
      $('body').once('savethebw-flyouts').each(() => {
        // Main menu hovers and keyboard accessibility
        const dropdowns = document.querySelectorAll('.menu--main .dropdown');
        map((item) => {
          if (item.querySelector('.block-content--type-menu-feature')) {
            item.classList.add('has-menu-feature');
          }
          const trigger = item.querySelector('.dropdown-toggle');
          const menu = item.querySelector('.dropdown-menu');
          const links = menu.querySelectorAll('a');
          const hideMenu = () => {
            menu.classList.remove('show');
            menu.parentNode.classList.remove('show');
            trigger.setAttribute('aria-expanded', 'false');
          };
          const showMenu = () => {
            menu.classList.add('show');
            menu.parentNode.classList.add('show');
            trigger.setAttribute('aria-expanded', 'true');
            map((dropdown) => {
              if (dropdown !== item) {
                dropdown.querySelector('.dropdown-toggle').blur();
              }
            }, dropdowns);
          };
          // Hovers
          hoverintent(item, showMenu, hideMenu);
          // Expand dropdown on enter, space, or down arrow
          trigger.addEventListener('keydown', (evt) => {
            switch (evt.keyCode) {
              case 38: {
                showMenu();
                evt.stopPropagation();
                evt.preventDefault();
                const menuItems = menu.querySelectorAll('a');
                const last = menuItems[menuItems.length - 1];
                last.setAttribute('tabindex', '-1');
                if (menuItems) {
                  setTimeout(() => {
                    $(last).focus();
                    last.removeAttribute('tabindex');
                  }, 50);
                }
                break;
              }
              case 13:
              case 32:
              case 40: {
                showMenu();
                evt.stopPropagation();
                evt.preventDefault();
                const first = menu.querySelector('a');
                first.setAttribute('tabindex', '-1');
                if (first) {
                  setTimeout(() => {
                    $(first).focus();
                    first.removeAttribute('tabindex');
                  }, 50);
                }
                break;
              }
              default:
                break;
            }
          });
          // Collapse dropdown on ESC and refocus triggering element
          item.addEventListener('keydown', (evt) => {
            switch (evt.keyCode) {
              case 27:
                hideMenu();
                trigger.focus();
                break;
              default:
                break;
            }
          });
          // When a trigger is focused, collapse any dropdown menus that aren't
          // in the same dropdown container. I.e. when tabbing forward to the
          // next top level menu item.
          trigger.addEventListener('focus', () => {
            map((dropdown) => {
              if (dropdown !== item) {
                const m = dropdown.querySelector('.dropdown-menu');
                m.classList.remove('show');
                m.parentNode.classList.remove('show');
                dropdown.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
              }
            }, dropdowns);
          });
          // When losing focus, if we didn't move to a child element, collapse
          // the dropdown. I.e. when tabbing backwards.
          trigger.addEventListener('focusout', (evt) => {
            if (evt.relatedTarget) {
              if (!includes(evt.relatedTarget.classList, 'menu-level-1') &&
                  !includes(evt.relatedTarget.classList, 'field-group-link')) {
                hideMenu();
              }
            }
          });
          // Handle arrow navigation on links within dropdowns.
          map((link) => {
            link.addEventListener('keydown', (evt) => {
              switch (evt.keyCode) {
                case 40:
                  $(evt.target)
                    .parent()
                    .next('.dropdown-item')
                    .find('a')
                    .focus();
                  break;
                case 38: {
                  $(evt.target)
                    .parent()
                    .prev('.dropdown-item')
                    .find('a')
                    .focus();
                  const $parent = $(evt.target).parent();
                  if ($parent.prev().length === 0) {
                    hideMenu();
                    trigger.focus();
                  }
                  break;
                }
                case 37: {
                  const $prev = $(evt.target).closest('.dropdown').prev('.dropdown').find('.dropdown-toggle');
                  if ($prev.length) {
                    $prev.focus();
                  } else {
                    hideMenu();
                    document.querySelector('a.navbar-brand').focus();
                  }
                  break;
                }
                case 39: {
                  const $next = $(evt.target)
                    .closest('.dropdown')
                    .next('.dropdown')
                    .find('.dropdown-toggle');
                  if ($next.length) {
                    $next.focus();
                  } else {
                    $(evt.target)
                      .closest('.dropdown')
                      .next('.nav-item')
                      .find('a.menu-level-0')
                      .focus();
                  }
                  break;
                }
                default:
                  break;
              }
            });
          }, links);
          // When gaining focus on a top level link collapse the menu. I.e.
          // when tabbing forward to the next top level item that is not a
          // dropdown.
          document.querySelector('a.menu-level-0').addEventListener('focus', hideMenu);
          // Arrow key navigation on top level menu items
          const topLevelLinks = document.querySelectorAll('.menu-level-0');
          map((link) => {
            link.addEventListener('keydown', (evt) => {
              switch (evt.keyCode) {
                case 37:
                  $(evt.target)
                    .closest('.nav-item')
                    .prev('.nav-item')
                    .find('.menu-level-0')
                    .focus();
                  break;
                case 39:
                  $(evt.target)
                    .closest('.nav-item')
                    .next('.nav-item')
                    .find('.menu-level-0')
                    .focus();
                  break;
                default:
                  break;
              }
            });
          }, topLevelLinks);
        }, dropdowns);
      });
    },
  };
})(jQuery, Drupal);
