import $ from 'external/jquery';
import AtkPlugin from './atk.plugin';

export default class AtkCreateModalPlugin extends AtkPlugin {
    main() {
        const options = this.settings;
        // make sure we have an object when no option is passed
        if ($.isArray(options.uriOptions)) {
            options.uriOptions = {};
        }
        // create modal and add it to the DOM
        const $m = $('<div class="atk-modal ui modal" />')
            .appendTo('body')
            .html(this.getDialogHtml(options.title));

        // add setting to our modal for modalService
        $m.data({
            uri: options.uri,
            type: options.dataType,
            args: options.uriOptions,
            needRemove: true,
            needCloseTrigger: true,
            label: options.label,
        });

        // call Fomantic-UI modal
        $m.modal(options.modal).modal('show');
        $m.addClass(this.settings.modalCss);
    }

    getDialogHtml(title) {
        return `<i class="icon close"></i>
          <div class="${this.settings.headerCss}">${title}</div>
          <div class="${this.settings.contentCss} content atk-dialog-content">
            </div>
          </div>`;
    }
}

AtkCreateModalPlugin.DEFAULTS = {
    title: '',
    uri: null,
    uriOptions: {},
    headerCss: 'header',
    modalCss: 'scrolling',
    contentCss: 'image',
    label: 'Loading...',
    modal: {
        duration: 100,
    },
};
