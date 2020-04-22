import $ from 'jquery';
import atkPlugin from './atk.plugin';

/**
 * Will expand or collapse an admin layout
 * menu items.
 * Toggling is done when clicking the toggler element.
 *    - when toggled icon class name are supplied and switch ex: caret left to caret down.
 * Clicking on a menu group will simulate a click event on the first menu item in the group.
 *
 * Default value are set for Maestro admin layout.
 */

export default class ajaxec extends atkPlugin {

  main() {
    // menu items container.
    this.menu = this.$el.find(this.settings.menuItemsSelector);
    if (this.menu.length === 0) {
      // this $el is our single item.
      if (this.urlMatchLocation(this.$el[0].href)){
        this.$el.addClass(this.settings.menuItemActiveClass);
      }
      return;
    }
    // html element for display or hiding menu items. Usually a div containning an icon.
    this.toggler = this.$el.find(this.settings.toggleSelector);

    this.addClickHandler();
    if (this.hasBase()) {
      // make menu group active.
      this.$el.addClass(this.settings.menuGroupActiveClass);
      // make menu group visible.
      this.menu.toggleClass(this.settings.visibleCssClass);
    }
    this.setTogglerIcon(this.settings.icon.selector);
  }

  /**
   * Check if the url correspond to one of our menu items.
   * if so, then add the menuItemActiveCSS class and return true.
   *
   * @returns {boolean}
   */
  hasBase() {
    const that = this;
    let hasBase = false;
    this.menu.find('a').each(function (idx, el) {
      if (that.urlMatchLocation(el.href)) {
        hasBase = true;
        // set active class for this specific menu item.
        $(el).addClass(that.settings.menuItemActiveClass);
      }
    });
    return hasBase;
  }

  /**
   * Check if an url match with current window location.
   * @param refUrl
   *
   * @return bool
   */
  urlMatchLocation(refUrl) {
    const url = new URL(refUrl);
    if (url.pathname === window.location.pathname) {
      return true;
    }
    // try to match base index url
    if (url.pathname === (window.location.pathname + this.settings.base)) {
      return true;
    }

    return false;
  }

  /**
   * Check if menu container for menu items contains the css visible class name.
   * Usually means that the menu items in a group are being display by css rule.
   *
   * @returns {*}
   */
  isMenuOn() {
    return this.menu.hasClass(this.settings.visibleCssClass);
  }

  /**
   * Set class icon for the toggler element.
   *
   * @param selector
   */
  setTogglerIcon(selector) {
    this.toggler.find(selector).attr('class', this.isMenuOn() ? this.settings.icon.off : this.settings.icon.on);
  }

  /**
   * Add click handler for menu group
   * and toggler element.
   */
  addClickHandler() {
    const that = this;
    this.$el.on('click', function(e) {
      // simulate click event on first menu item in group.
      that.menu.find('a').first()[0].click();
      that.menu.toggleClass(that.settings.visibleCssClass);
    });
    this.toggler.on('click', function(e) {
      e.stopPropagation();
      e.preventDefault();
      that.menu.toggleClass(that.settings.visibleCssClass);
      that.setTogglerIcon(that.settings.icon.selector);
    });
  }
}

ajaxec.DEFAULTS = {
  base: 'index.php',
  menuItemsSelector : '.atk-maestro-menu-items', // The css selector where menu items are contain.
  toggleSelector: '.atk-submenu-toggle', // the css selector that will show or hide sub menu.
  visibleCssClass: 'atk-visible', // Display an item when this css class is set.
  menuGroupActiveClass: 'active', // the css class to set when a menu group is active.
  menuItemActiveClass: 'active', // the css class to set when a menu item in a group is active.
  icon : {
    selector: 'i',
    on: 'icon caret right',
    off: 'icon caret down',
  },
};
