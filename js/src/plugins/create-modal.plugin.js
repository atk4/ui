import $ from 'external/jquery';
import AtkPlugin from './atk.plugin';

export default class AtkCreateModalPlugin extends AtkPlugin {
    main() {
        const options = this.settings;
        // make sure we have an object when no option is passed
        if ($.isArray(options.urlOptions)) {
            options.urlOptions = {};
        }
        // create modal and add it to the DOM
        const $m = $('<div class="atk-modal ui modal" />')
            .appendTo('body')
            .html(this.getDialogHtml(options.title));

        // add setting to our modal for modalService
        $m.data({
            url: options.url,
            type: options.dataType,
            args: options.urlOptions,
            needRemove: true,
            loadingLabel: options.loadingLabel,
        });

        // call Fomantic-UI modal
        $m.modal(options.modal).modal('show');
        $m.addClass(this.settings.modalCss);
    }

    getDialogHtml(title) {
        return `<i class="close icon"></i>
          ` + (title ? `<div class="${this.settings.headerCss}">${title}</div>
          ` : '') + `<div class="${this.settings.contentCss} content atk-dialog-content">
            </div>
          </div>`;
    }
}

AtkCreateModalPlugin.DEFAULTS = {
    title: '',
    url: null,
    urlOptions: {},
    headerCss: 'header',
    modalCss: 'scrolling',
    contentCss: 'image',
    loadingLabel: 'Loading...',
    modal: {},
};
