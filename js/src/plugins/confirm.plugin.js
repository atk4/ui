import $ from 'jquery';
import AtkPlugin from './atk.plugin';

/**
 * A Fomantic-UI Modal dialog for confirming an action.
 *
 * Will execute onApprove function when user click ok button;
 * Will execute onDeny function when user click cancel button.
 *
 * Fomantic-UI modal option can be pass using modalOptions object.
 * Setting onApprove and onDeny function within modalOptions object will override
 * onApprove and onDeny current setting.
 */
export default class AtkConfirmPlugin extends AtkPlugin {
    main() {
        let context = this;
        const $m = $('<div class="ui modal" />')
            .appendTo('body')
            .html(this.getDialogHtml(this.settings.message));

        $m.addClass(this.settings.size);

        let options = {};

        if (this.settings.context) {
            context = this.settings.context;
        }

        // Create wrapper function for using proper "this" context.
        if (this.settings.onApprove) {
            options.onApprove = () => { this.settings.onApprove.call(context); };
        }
        if (this.settings.onDeny) {
            options.onDeny = () => { this.settings.onDeny.call(context); };
        }

        options = Object.assign(options, this.settings.modalOptions);

        $m.data('needRemove', true).modal(options).modal('show');
    }

    getDialogHtml(message) {
        return `
          <div class=" content">${message}</div>
          <div class="actions">
            <div class="ui approve primary button">${this.settings.options.button.ok}</div>
            <div class="ui cancel button">${this.settings.options.button.cancel}</div>
           </div>
          `;
    }
}

AtkConfirmPlugin.DEFAULTS = {
    message: null,
    size: 'tiny',
    onApprove: null,
    onDeny: null,
    options: { button: { ok: 'Ok', cancel: 'Cancel' } },
    modalOptions: { closable: false },
    context: null,
};
