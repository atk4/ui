import $ from 'jquery';
import atkPlugin from './atk.plugin';

export default class ajaxec extends atkPlugin {

  main() {
    // grap menu items container.
    this.menu = this.$el.find(this.settings.menuItemsSelector);
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
      if (el.href.includes(that.settings.base)) {
        hasBase = true;
        // set active class for this specific menu item.
        $(el).addClass(that.settings.menuItemActiveClass);
      }
    });
    return hasBase;
  }

  isMenuOn() {
    return this.menu.hasClass(this.settings.visibleCssClass);
  }

  setTogglerIcon(selector) {
    this.toggler.find(selector).attr('class', this.isMenuOn() ? this.settings.icon.off : this.settings.icon.on);
  }

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
  base: null, // the url to match a menu item.
  menuItemsSelector : '.atk-admin-menu-items', // The css selector where menu items are contain.
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
