import $ from 'external/jquery';
import AtkPlugin from './atk.plugin';

/**
 * Add dynamic scrolling to a View that can accept page argument in URL.
 *
 * default options are:
 * padding: 20         The amount of padding needed prior to request a page load.
 * initialPage: 1      The initial page load when calling this plugin.
 * appendTo: null      The HTML element where new content should be append to.
 * stateContext: null  A jQuery selector, where you would like Fomantic-UI, to apply the stateContext to during the api call. if null, then a default loader will be apply to the bottom of the $inner element.
 */
export default class AtkScrollPlugin extends AtkPlugin {
    main() {
        // check if we are initialized already because loading content
        // can recall this plugin and screw up page number
        if (this.$el.data('__atkScroll')) {
            return false;
        }

        const defaultSettings = {
            padding: 20,
            initialPage: 1,
            appendTo: null,
            hasFixTableHeader: false,
            tableContainerHeight: 400,
            tableHeaderColor: '#ffffff',
            stateContext: null,
        };
        // set default option if not set
        this.settings.options = { ...defaultSettings, ...this.settings.options };

        this.isWaiting = false;
        this.nextPage = this.settings.options.initialPage + 1;

        if (this.settings.options.hasFixTableHeader) {
            this.isWindow = false;
            this.$scroll = this.$el.parent();
            this.$inner = this.$el;
            this.setTableHeader();
        } else {
            // check if scroll apply vs Window or inside our element
            this.isWindow = this.$el.css('overflow-y') === 'visible';
            this.$scroll = this.isWindow ? $(window) : this.$el;
            // is Inner the element itself or it's children
            this.$inner = this.isWindow ? this.$el : this.$el.children();
        }

        // the target element within container where new content is appendTo
        this.$target = this.settings.options.appendTo ? this.$inner.find(this.settings.options.appendTo) : this.$inner;

        this.$scroll.on('scroll', this.onScroll.bind(this));

        // if there is no scrollbar, then try to load next page too
        if (!this.hasScrollbar()) {
            this.loadContent();
        }
    }

    /**
     * Add fix table header.
     */
    setTableHeader() {
        if (this.$el.parent().length > 0) {
            let $tableCopy = null;
            this.$el.parent().height(this.settings.options.tableContainerHeight);
            this.$el.addClass('fixed');
            $tableCopy = this.$el.clone(true, true);
            $tableCopy.attr('id', $tableCopy.attr('id') + '_');
            $tableCopy.find('tbody, tfoot').remove();
            $tableCopy.css({
                position: 'absolute',
                'background-color': this.settings.options.tableHeaderColor,
                border: this.$el.find('th').eq(1).css('border-left'),
                'z-index': 1,
            });
            this.$scroll.prepend($tableCopy);
            this.$el.find('thead').hide();
            this.$el.css('margin-top', $tableCopy.find('thead').height());
        }
    }

    /**
     * Check if scrolling require adding content.
     */
    onScroll(event) {
        const borderTopWidth = Number.parseInt(this.$el.css('borderTopWidth'), 10);
        const borderTopWidthInt = Number.isNaN(borderTopWidth) ? 0 : borderTopWidth;
        // this.$el padding top value
        const paddingTop = Number.parseInt(this.$el.css('paddingTop'), 10) + borderTopWidthInt;
        // either the scroll bar position using window or the container element top position otherwise
        const topHeight = this.isWindow ? $(window).scrollTop() : this.$scroll.offset().top;
        // Inner top value. If using Window, this value does not change, otherwise represent the inner element top value when scroll.
        const innerTop = this.$inner.length > 0 ? this.$inner.offset().top : 0;
        // the total height
        const totalHeight = Math.ceil(topHeight - innerTop + this.$scroll.height() + paddingTop);

        if (!this.isWaiting && totalHeight + this.settings.options.padding >= this.$inner.outerHeight()) {
            this.loadContent();
        }
    }

    /**
     * Check if container element has vertical scrollbar.
     *
     * @returns {boolean}
     */
    hasScrollbar() {
        const innerHeight = this.isWindow ? Math.ceil(this.$el.height()) : Math.ceil(this.$inner.height());
        const scrollHeight = Math.ceil(this.$scroll.height());

        return innerHeight > scrollHeight;
    }

    /**
     * Put scroll in idle mode.
     */
    idle() {
        this.isWaiting = true;
    }

    /**
     * Ask server for more content.
     */
    loadContent() {
        if (!this.settings.options.stateContext) {
            this.addLoader();
        }

        this.isWaiting = true;
        this.$inner.api({
            on: 'now',
            url: this.settings.url,
            data: { ...this.settings.urlOptions, page: this.nextPage },
            method: 'GET',
            stateContext: this.settings.options.stateContext,
            onComplete: this.onComplete.bind(this),
        });
    }

    /**
     * Use response to append content to element and setup next content to be loaded.
     * Set response.id to null in order for apiService.onSuccess to bypass
     * replacing HTML content. JS returned from server response will still be executed.
     */
    onComplete(response, element) {
        this.removeLoader();
        if (response.success) {
            if (response.html) {
                this.$target.append(response.html);
                if (response.noMoreScrollPages) {
                    this.idle();
                } else {
                    this.isWaiting = false;
                    this.nextPage++;
                    // if there is no scrollbar, then try to load next page too
                    if (!this.hasScrollbar()) {
                        this.loadContent();
                    }
                }
            }

            response.id = null;
        }
    }

    addLoader() {
        const $parent = this.$inner.parent().hasClass('atk-overflow-auto') ? this.$inner.parent().parent() : this.$inner.parent();
        $parent.append($('<div id="atkScrollLoader"><div class="ui section hidden divider"></div><div class="ui active centered inline loader basic segment"></div></div>'));
    }

    removeLoader() {
        $('#atkScrollLoader').remove();
    }
}

AtkScrollPlugin.DEFAULTS = {
    url: null,
    urlOptions: {},
    options: {},
};
