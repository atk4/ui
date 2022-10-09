import $ from 'external/jquery';
/* global Draggable */ // loaded after main JS
import atk from 'atk';
import AtkPlugin from './atk.plugin';

/**
 * Make elements inside a container draggable and sortable.
 * Use shopify/Draggable library https://github.com/Shopify/draggable,
 * draggable.js is external to this bundle, so it need to be load from CDN.
 *
 * After reordering, callback is sent to server with post information:
 * order => contains the order of data-{label} as a comma delimited string;
 * source => the element being reorder.
 * pos => the final position of the element being reorder.
 *
 * Defaut container is set to table boddy (tbody), using table row(tr) as reoderable element.
 * To use other container, simply set container and draggable accordingly.
 * $sortable = JsSortable::addTo($lister, ['container' => 'ul', 'draggable' => 'li', 'dataLabel' => 'name']);
 *
 * Element containing specific css class can be used as the handle for dragging element, if null
 * is pass, than the entire element is used.
 */
export default class AtkJsSortablePlugin extends AtkPlugin {
    main() {
        this.ids = [];
        // the data label attribute value of the source element being drag. ex: data-id
        this.sourceId = null;
        // the new index value of the dragged element after sorting.
        this.newIdx = null;
        // the original index value of the dragged element.
        this.orgIdx = null;

        this.injectStyles(this.settings.mirrorCss + this.settings.overCss);
        this.dragContainer = this.$el.find(this.settings.container);
        const sortable = new Draggable.Sortable(
            this.dragContainer[0],
            {
                draggable: this.settings.draggable,
                handle: this.settings.handleClass ? '.' + this.settings.handleClass : null,
            },
        );
        this.initialize();

        sortable.on('sortable:stop', (e) => {
            this.ids = [];
            this.newIdx = e.data.newIndex;
            this.orgIdx = e.data.oldIndex;
            this.sourceId = $(e.data.dragEvent.data.originalSource).data(this.settings.dataLabel);
            this.dragContainer.children().each((idx, el) => {
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
        this.dragContainer.children().each((idx, el) => {
            this.ids.push($(el).data(this.settings.dataLabel));
        });
    }

    /**
     * Will send current element order via callback.
     *
     * @param {object} params Extra arguments to add to uri.
     */
    getSortOrders(params) {
        this.sendSortOrders(params);
    }

    /**
     * Send orders to server via JsCallback.
     */
    sendSortOrders(params) {
        const url = this.buildUrl(params);
        if (url) {
            this.dragContainer.api({
                on: 'now',
                url: url,
                data: {
                    order: this.ids.toString(), orgIdx: this.orgIdx, newIdx: this.newIdx, source: this.sourceId,
                },
                method: 'POST',
                obj: this.dragContainer,
            });
        }
    }

    buildUrl(extraParams = null) {
        let url = null;
        if (this.settings.uriOptions && extraParams) {
            url = atk.urlHelper.appendParams(this.settings.uri, $.extend({}, this.settings.uriOptions, extraParams));
        } else if (this.settings.uriOptions) {
            url = atk.urlHelper.appendParams(this.settings.uri, this.settings.uriOptions);
        } else {
            url = this.settings.uri;
        }

        return url;
    }

    injectStyles(style) {
        $('head').append('<style>' + style + '</style>');
    }
}

AtkJsSortablePlugin.DEFAULTS = {
    uri: null,
    uriOptions: null,
    container: 'tbody',
    draggable: 'tr',
    dataLabel: 'id',
    handleClass: null,
    mirrorCss: '.draggable-mirror { background: #fff!important; margin-left: 1%; opacity: 0.9; }',
    overCss: '.draggable--over { background: yellow !important; opacity: 0.5; }',
    autoFireCb: true,
};
