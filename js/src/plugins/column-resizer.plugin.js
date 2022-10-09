import $ from 'external/jquery';
import Resizer from 'column-resizer';
import AtkPlugin from './atk.plugin';

/**
 * Enable table column to be resizable using drag.
 */
export default class AtkColumnResizerPlugin extends AtkPlugin {
    main() {
        // add on resize callback if url is supply.
        if (this.settings.uri) {
            this.settings.onResize = this.onResize.bind(this);
        }
        this.resizable = new Resizer(this.$el[0], ({ ...this.settings.atkDefaults, ...this.settings }));

        // reset padding class.
        this.$el.removeClass('grip-padding');
    }

    /**
     * Send widths to server via callback uri.
     *
     * @param {Array.<object>} widths an Array of objects, each containing the column name and their size in pixels [{column: 'name', size: '135px'}]
     */
    sendWidths(widths) {
        this.$el.api({
            on: 'now',
            url: this.settings.uri,
            method: 'POST',
            data: { widths: JSON.stringify(widths) },
        });
    }

    /**
     * On resize callback when user finish dragging column for resizing.
     * Calling this method via callback need to bind "this" set to this plugin.
     */
    onResize(event) {
        const columns = this.$el.find('th');

        const widths = [];
        columns.each((idx, item) => {
            widths.push({ column: $(item).data('column'), size: $(item).outerWidth() });
        });

        this.sendWidths(widths);
    }
}

AtkColumnResizerPlugin.DEFAULTS = {
    atkDefaults: {
        liveDrag: true,
        resizeMode: 'overflow',
        draggingClass: 'atk-column-dragging',
        minWidth: 8,
        // onResize: function(e) { e.path.filter(function(item) { return item.querySelector('table') }); }
    },
    uri: null,
};
