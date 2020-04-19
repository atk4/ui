import $ from 'jquery';
import atkPlugin from './atk.plugin';

export default class ajaxec extends atkPlugin {

  main() {
    // grap submenu.
    console.log(this.$el);
    this.menu = this.$el.find(this.settings.menuItemsSelector);
    this.$copy = this.menu.clone().addClass('ui vertical inverted atk-admin-sub-menu-temp');

    this.addClickHandler();
    this.addHoverHandler();
    if (this.hasBase()) {
      this.$el.addClass('atk-group-active');
      this.menu.toggleClass(this.settings.visibleCssClass);
    };
  }

  hasBase() {
    const that = this;
    let hasBase = false;
    this.menu.children('a').each(function (idx, el) {
      //console.log(el.href);
      if (el.href.includes(that.settings.base)) {
        hasBase = true;
        $(el).addClass('active');
      }
    });
    return hasBase;
  }

  displaySideMenu() {
    const that = this;
    const top = this.menu.parent().position().top + 47;
    const left = this.settings.menuWidth;

    const style = `
      position: absolute;
      z-index: 204;
      top: ${top}px;
      left: ${left}px;
      width: ${this.settings.subMenuWidth};
      height: fit-content;
    `;
    // add hover handlers to our copied menu.
    this.$copy.hover(function(e) {
      // clear timeout for submenu to stay open.
      clearTimeout(atk.menuOutTimer);
    }, function(e) {
      // hide menu on leave.
      that.hideSideMenu();
    });
    $('.atk-admin-sub-menu').append(this.$copy);
    this.$copy.css('cssText', style);
  }

  /**
   * Remove sub copied menu.
   */
  hideSideMenu() {
    this.$copy.remove();
  }

  /**
   * Remove all copied sub-menu left open inside our temp container.
   */
  hideAllSideMenu() {
    $(this.settings.subMenusSelector + ' .atk-admin-sub-menu-temp').each(function(idx, el) {
      $(el).remove();
    })
  }

  addHoverHandler() {
    const that = this;
    this.$el.hover(function(e) {
      clearTimeout(atk.menuOutTimer);
      that.hideAllSideMenu();
      atk.menuInTimer = setTimeout(that.displaySideMenu.bind(that), 250);
    }, function(e) {
      clearTimeout(atk.menuInTimer);
      atk.menuOutTimer = setTimeout(that.hideSideMenu.bind(that), 500);
    })
  }

  addClickHandler() {
    const that = this;
    this.$el.on('click', function(e) {
      if (that.$copy.is(':visible')) {
        that.menu.find('a').first()[0].click();
      } else {
        that.menu.toggleClass(that.settings.visibleCssClass);
      }
    });
    this.menu.find('.item').on('click', function(e) {e.stopPropagation()});
    this.menu.find('.item').on('hover', function(e) {e.stopPropagation()});
  }

  // hideOthers() {
  //   const that = this;
  //   $(this.settings.menuSelector).each(function(idx, el) {
  //     if (!($(el).attr('id') === that.$el.attr('id'))) {
  //       $(el).find(that.settings.menuItemsSelector).transition();
  //     }
  //   })
  // }
}

ajaxec.DEFAULTS = {
  base: null,
  menuSelector: '.atk-admin-left-menu',
  menuItemsSelector : 'div.menu',
  subMenusSelector: '.atk-admin-sub-menu',
  visibleCssClass: 'atk-visible',
  menuWidth: 260,
  subMenuWidth: 120,
};
