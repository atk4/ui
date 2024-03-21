import $ from 'external/jquery';
/* global Draggable */ // loaded after main JS
import atk from 'atk';
import AtkPlugin from './atk.plugin';

/**
 * Make elements inside a container draggable and sortable.
 * Use shopify/Draggable library https://github.com/Shopify/draggable,
 * draggable.js is external to this bundle, so it needs to be loaded from CDN.
 *
 * After reordering, callback is sent to server with post information:
 * order => contains the order of data-{label} as a comma delimited string;
 * source => the element being reorder.
 * pos => the final position of the element being reorder.
 *
 * Default container is set to table body (tbody), using table row(tr) as reoderable element.
 * To use other container, simply set container and draggable accordingly.
 * $sortable = JsSortable::addTo($lister, ['container' => 'ul', 'draggable' => 'li', 'dataLabel' => 'name']);
 *
 * Element containing specific CSS class can be used as the handle for dragging element, if null
 * is pass, than the entire element is used.
 */
export default class AtkJsSortablePlugin extends AtkPlugin {
    main() {
        this.ids = [];
        // the data label attribute value of the source element being drag. ex: data-id
        this.sourceId = null;
        // the new index value of the dragged element after sorting
        this.newIndex = null;
        // the original index value of the dragged element
        this.origIndex = null;

        // fix screen reader announcement container added more than once
        // https://github.com/Shopify/draggable/pull/541
        for (let elem; elem = document.querySelector('#draggable-live-region');) { // eslint-disable-line no-cond-assign
            elem.remove();
        }

        this.injectStyles(this.settings.mirrorCss + this.settings.overCss);
        this.dragContainer = this.$el.find(this.settings.container);
        const sortable = new Draggable.Sortable(
            this.dragContainer[0],
            {
                draggable: this.settings.draggable,
                handle: this.settings.handleClass ? '.' + this.settings.handleClass : null,
            }
        );
        this.initialize();

        sortable.on('sortable:stop', (e) => {
            if (e.data.newIndex === e.data.oldIndex) {
                return;
            }

            this.ids = [];
            this.newIndex = e.data.newIndex;
            this.origIndex = e.data.oldIndex;
            this.sourceId = $(e.data.dragEvent.data.originalSource).data(this.settings.dataLabel);
            this.dragContainer.children().each((i, el) => {
                if (!$(el).hasClass('draggable--original') && !$(el).hasClass('draggable-mirror')) {
                    this.ids.push($(el).data(this.settings.dataLabel));
                }
            });
            if (this.settings.autoFireCb) {
                this.sendSortOrders();
            }
        });
    }

    initialize() {
        this.dragContainer.children().each((i, el) => {
            this.ids.push($(el).data(this.settings.dataLabel));
        });
    }

    /**
     * Send orders to server via JsCallback.
     *
     * @param {object} params Extra arguments to add to URL.
     */
    sendSortOrders(params) {
        const url = this.buildUrl(params);
        if (url) {
            this.dragContainer.api({
                on: 'now',
                url: url,
                data: {
                    order: this.ids.toString(), origIndex: this.origIndex, newIndex: this.newIndex, source: this.sourceId,
                },
                method: 'POST',
                obj: this.dragContainer,
            });
        }
    }

    buildUrl(extraParams = null) {
        let url = null;
        if (this.settings.urlOptions && extraParams) {
            url = atk.urlHelper.appendParams(this.settings.url, $.extend({}, this.settings.urlOptions, extraParams));
        } else if (this.settings.urlOptions) {
            url = atk.urlHelper.appendParams(this.settings.url, this.settings.urlOptions);
        } else {
            url = this.settings.url;
        }

        return url;
    }

    injectStyles(style) {
        $('head').append('<style>' + style + '</style>');
    }
}

AtkJsSortablePlugin.DEFAULTS = {
    url: null,
    urlOptions: null,
    container: 'tbody',
    draggable: 'tr',
    dataLabel: 'id',
    handleClass: null,
    mirrorCss: '.draggable-mirror { background: #fff!important; margin-left: 1%; opacity: 0.9; }',
    overCss: '.draggable--over { background: yellow !important; opacity: 0.5; }',
    autoFireCb: true,
};
