import $ from 'external/jquery';
import AtkPlugin from './atk.plugin';

/**
 * Will expand or collapse menu items for side navigation.
 * Toggling is done when clicking the toggler element.
 * - Toggling icon class name will be switch ex: caret left to caret down, when triggered.
 * Clicking on a menu group will simulate a click event on the first menu item in the group.
 *
 * Default value are set for Maestro admin layout.
 */
export default class AtkSidenavPlugin extends AtkPlugin {
    main() {
        // menu items container
        this.menu = this.$el.find(this.settings.menuItemsSelector);
        if (this.menu.length === 0) {
            // this $el is our single item
            if (this.urlMatchLocation(this.$el[0].href)) {
                this.$el.addClass(this.settings.menuItemActiveClass);
            }

            return;
        }
        // HTML element for display or hiding menu items. Usually a div containning an icon.
        this.toggler = this.$el.find(this.settings.toggleSelector);

        this.addClickHandler();
        if (this.hasBase()) {
            // make menu group active
            this.$el.addClass(this.settings.menuGroupActiveClass);
            // make menu group visible
            this.menu.toggleClass(this.settings.visibleCssClass);
        }
        this.setTogglerIcon(this.settings.icon.selector);
    }

    /**
     * Check if the URL correspond to one of our menu items.
     * if so, then add the menuItemActiveCSS class and return true.
     *
     * @returns {boolean}
     */
    hasBase() {
        let hasBase = false;
        this.menu.find('a').each((i, el) => {
            if (this.urlMatchLocation(el.href)) {
                hasBase = true;
                // set active class for this specific menu item
                $(el).addClass(this.settings.menuItemActiveClass);
            }
        });

        return hasBase;
    }

    /**
     * Check if an URL match with current window location.
     *
     * @returns {boolean}
     */
    urlMatchLocation(refUrl) {
        const url = new URL(refUrl);
        if (url.pathname === window.location.pathname) {
            return true;
        }
        // try to match base index URL
        if (url.pathname === (window.location.pathname + this.settings.base)) {
            return true;
        }

        return false;
    }

    /**
     * Check if menu container for menu items contains the CSS visible class name.
     * Usually means that the menu items in a group are being display by CSS rule.
     *
     * @returns {*}
     */
    isMenuOn() {
        return this.menu.hasClass(this.settings.visibleCssClass);
    }

    /**
     * Set class icon for the toggler element.
     */
    setTogglerIcon(selector) {
        this.toggler.find(selector).attr('class', (this.isMenuOn() ? this.settings.icon.off : this.settings.icon.on) + ' icon');
    }

    /**
     * Add click handler for menu group
     * and toggler element.
     */
    addClickHandler() {
        this.$el.find(this.settings.menuGroupTitleSelector).on('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            window.open(this.menu.find(this.settings.firstItemSelector).first().attr('href'), e.metaKey ? '_blank' : '_self');
        });
        this.toggler.on('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.menu.toggleClass(this.settings.visibleCssClass);
            this.setTogglerIcon(this.settings.icon.selector);
        });
    }
}

AtkSidenavPlugin.DEFAULTS = {
    base: 'index.php',
    menuItemsSelector: '.atk-maestro-menu-items', // the CSS selector where menu items are contain
    menuGroupTitleSelector: '.atk-menu-group-title', // the CSS selector for menu group title
    toggleSelector: '.atk-submenu-toggle', // the CSS selector that will show or hide sub menu
    visibleCssClass: 'atk-visible', // display an item when this CSS class is set
    menuGroupActiveClass: 'active', // the CSS class to set when a menu group is active
    menuItemActiveClass: 'active', // the CSS class to set when a menu item in a group is active
    firstItemSelector: 'a', // the selector for the first menu item in a group, where click will be trigger
    icon: {
        selector: 'i',
        on: 'caret right',
        off: 'caret down',
    },
};
