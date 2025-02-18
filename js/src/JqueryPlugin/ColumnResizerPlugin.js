import $ from 'external/jquery';
import Resizer from 'column-resizer';
import AbstractPlugin from './AbstractPlugin';

/**
 * Enable table column to be resizable using drag.
 */
export default class AtkColumnResizerPlugin extends AbstractPlugin {
    main() {
        this.settings.onResize = this.onResize.bind(this);
        this.resizable = new Resizer($(this.element)[0], { ...this.settings.atkDefaults, ...this.settings });

        // reset padding class
        $(this.element).removeClass('grip-padding');
    }

    /**
     * Send widths to server via callback URL.
     *
     * @param {Array.<object>} widths example: [{ column: 'name', size: 135 }]
     */
    sendWidths(widths) {
        $(this.element).api({
            on: 'now',
            url: this.settings.url,
            method: 'POST',
            data: { widths: JSON.stringify(widths) },
        });
    }

    onResize(event) {
        if (this.settings.url) {
            const columns = $(this.element).find('th');

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
