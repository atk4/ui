import $ from 'jquery';
import atkPlugin from './atk.plugin';

/**
 * Add dynamic scrolling to a View that can accept page argument in URL.
 *
 * default options are:
 *  padding: 20         The amount of padding needed prior to request a page load.
 *  initialPage: 1      The initial page load when calling this plugin.
 *  appendTo: null      The html element where new content should be append to.
 *  allowJsEval: false  Whether or not javascript send in server response should be evaluate.
 *  stateContext: null  A jQuery selector, where you would like fomantic-ui, to apply the stateContext to during the api call.
 *                        if null, then a default loader will be apply to the bottom of the $inner element.
 */

export default class scroll extends atkPlugin {
    main() {
    // check if we are initialized already because loading content
    // can recall this plugin and screw up page number.
        if (this.$el.data('__atkScroll')) {
            return false;
        }

        const defaultSettings = {
            padding: 20,
            initialPage: 1,
            appendTo: null,
            allowJsEval: false,
            hasFixTableHeader: false,
            tableContainerHeight: 400,
            tableHeaderColor: '#ffffff',
            stateContext: null,
        };
        // set default option if not set.
        this.settings.options = { ...defaultSettings, ...this.settings.options };

        this.isWaiting = false;
        this.nextPage = this.settings.options.initialPage + 1;

        if (this.settings.options.hasFixTableHeader) {
            this.isWindow = false;
            this.$scroll = this.$el.parent();
            this.$inner = this.$el;
            this.setTableHeader();
        } else {
            // check if scroll apply vs Window or inside our element.
            this.isWindow = (this.$el.css('overflow-y') === 'visible');
            this.$scroll = this.isWindow ? $(window) : this.$el;
            // is Inner the element itself or it's children.
            this.$inner = this.isWindow ? this.$el : this.$el.children();
        }

        // the target element within container where new content is appendTo.
        this.$target = this.settings.options.appendTo ? this.$inner.find(this.settings.options.appendTo) : this.$inner;

        this.bindScrollEvent(this.$scroll);

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
   * Bind scrolling event to an element.
   *
   * @param $el
   */
    bindScrollEvent($el) {
        $el.on('scroll', this.observe.bind(this));
    }

    /**
   * Check if scrolling require adding content.
   *
   * @param e // event
   */
    observe(e) {
        const borderTopWidth = parseInt(this.$el.css('borderTopWidth'), 10);
        const borderTopWidthInt = Number.isNaN(borderTopWidth) ? 0 : borderTopWidth;
        // this.$el padding top value.
        const paddingTop = parseInt(this.$el.css('paddingTop'), 10) + borderTopWidthInt;
        // Either the scroll bar position using window or the container element top position otherwise.
        const topHeight = this.isWindow ? $(window).scrollTop() : this.$scroll.offset().top;
        // Inner top value. If using Window, this value does not change, otherwise represent the inner element top value when scroll.
        const innerTop = this.$inner.length ? this.$inner.offset().top : 0;
        // The total height.
        const totalHeight = Math.ceil(topHeight - innerTop + this.$scroll.height() + paddingTop);

        if (!this.isWaiting && totalHeight + this.settings.options.padding >= this.$inner.outerHeight()) {
            this.loadContent();
        }
    }

    /**
   * Check if container element has vertical scrollbar.
   *
   * @return bool
   */
    hasScrollbar() {
        const innerHeight = this.isWindow ? Math.ceil(this.$el.height()) : Math.ceil(this.$inner.height());
        const scrollHeight = Math.ceil(this.$scroll.height());
        return innerHeight > scrollHeight;
    }

    /**
   * Set Next page to be loaded.
   *
   * @param page
   */
    setNextPage(page) {
        this.nextPage = page;
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
            url: this.settings.uri,
            data: { ...this.settings.uri_options, page: this.nextPage },
            method: 'GET',
            stateContext: this.settings.options.stateContext,
            onComplete: this.onComplete.bind(this),
        });
    }

    /**
   * Use response to append content to element and setup next content to be load.
   * Set response.id to null in order for apiService.onSuccess to bypass
   * replacing html content. Js return from server response will still be execute.
   *
   * @param response
   * @param element
   */
    onComplete(response, element) {
        this.removeLoader();
        if (response && response.success) {
            if (response.html) {
                // Done - no more pages
                if (response.message === 'Done') {
                    this.$target.append(response.html);
                    this.idle();
                }
                // Success - will have more pages
                if (response.message === 'Success') {
                    this.$target.append(response.html);
                    this.isWaiting = false;
                    this.nextPage++;
                    // if there is no scrollbar, then try to load next page too
                    if (!this.hasScrollbar()) {
                        this.loadContent();
                    }
                }
            }

            response.id = null;
            if (!this.settings.options.allowJsEval) {
                response.atkjs = null;
            }
        }
    }

    /**
   * Add loader.
   */
    addLoader() {
        const $parent = this.$inner.parent().hasClass('atk-overflow-auto') ? this.$inner.parent().parent() : this.$inner.parent();
        /* eslint-disable */
        $parent.append($('<div id="atkScrollLoader"><div class="ui section hidden divider"></div><div class="ui active centered inline loader basic segment"></div></div>'));
        /* eslint-enable */
    }

    /**
   * Remove loader.
   */
    removeLoader() {
        $('#atkScrollLoader').remove();
    }
}

scroll.DEFAULTS = {
    uri: null,
    uri_options: {},
    options: {},
};
