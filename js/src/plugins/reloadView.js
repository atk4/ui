import $ from 'jquery';

export default class reloadView {
    constructor(element, options) {
        const $element = $(element);

        if(options.callback) {
            $.get(options.callback, (data) => {
                $element.replaceWith(data);
            });
        }
    }
}

reloadView.DEFAULTS = {
    callback: null,
};