import $ from 'external/jquery';
import Resizer from 'column-resizer';
import AtkPlugin from './atk.plugin';

/**
 * Enable table column to be resizable using drag.
 */
export default class AtkColumnResizerPlugin extends AtkPlugin {
    main() {
        this.settings.onResize = this.onResize.bind(this);
        this.resizable = new Resizer(this.$el[0], { ...this.settings.atkDefaults, ...this.settings });

        // reset padding class
        this.$el.removeClass('grip-padding');
    }

    /**
     * Send widths to server via callback URL.
     *
     * @param {Array.<object>} widths example: [{ column: 'name', size: 135 }]
     */
    sendWidths(widths) {
        this.$el.api({
            on: 'now',
            url: this.settings.url,
            method: 'POST',
            data: { widths: JSON.stringify(widths) },
        });
    }

    onResize(event) {
        if (this.settings.url) {
            const columns = this.$el.find('th');

            const widths = [];
            columns.each((i, item) => {
                widths.push({ column: $(item).data('column'), size: $(item).outerWidth() });
            });

            this.sendWidths(widths);
        }
    }
}

AtkColumnResizerPlugin.DEFAULTS = {
    atkDefaults: {
        resizeMode: 'flex',
        liveDrag: true,
        draggingClass: 'atk-column-dragging',
        serialize: false,
    },
    url: null,
};
