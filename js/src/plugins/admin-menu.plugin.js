import $ from 'jquery';
import atkPlugin from './atk.plugin';

export default class ajaxec extends atkPlugin {

  main() {
    // grap submenu.
    this.menu = this.$el.find(this.settings.menuItemsSelector);
    this.$copy = this.menu.clone().addClass('ui vertical inverted atk-admin-sub-menu-temp');

    this.addClickHandler();
    this.addHoverHandler();
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
    this.$copy.hover(function(e) {
      clearTimeout(atk.menuInTimer);
    }, function(e) {
      that.hideSideMenu();
    });
    $('.atk-admin-sub-menu').append(this.$copy);
    this.$copy.css('cssText', style);
  }

  hideSideMenu() {
    console.log('removing copy');
    this.$copy.remove();
  }

  hideAllSideMenu() {
    $(this.settings.subMenusSelector + ' .atk-admin-sub-menu-temp').each(function(idx, el) {
      console.log();
      $(el).remove();
    })
  }

  addHoverHandler() {
    const that = this;
    this.$el.hover(function(e) {
      console.log('in')
      clearTimeout(atk.menuInTimer);
      that.hideAllSideMenu();
      that.displaySideMenu();
    }, function(e) {
      // set timer and clear menu.
      atk.menuInTimer = setTimeout(that.hideSideMenu.bind(that), 500);
    })
  }

  addClickHandler() {
    const that = this;
    this.$el.on('click', function(e) {
      that.menu.toggleClass(that.settings.visibleCssClass);
    });
    this.menu.find('.item').on('click', function(e) {e.stopPropagation()});
  }

  hideOthers() {
    const that = this;
    $(this.settings.menuSelector).each(function(idx, el) {
      if (!($(el).attr('id') === that.$el.attr('id'))) {
        $(el).find(that.settings.menuItemsSelector).transition();
      }
    })
  }
}

ajaxec.DEFAULTS = {
  menuSelector: '.atk-admin-left-menu-group',
  menuItemsSelector : 'div.menu',
  subMenusSelector: '.atk-admin-sub-menu',
  visibleCssClass: 'atk-visible',
  menuWidth: 260,
  subMenuWidth: 120,
};