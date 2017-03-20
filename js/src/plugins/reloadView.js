import $ from 'jquery';

export default class reloadView {
    constructor(element, options) {
        const $element = $(element);

        if(options.uri) {
            $.get(options.uri, options.uri_options, (data) => {
                $element.replaceWith(data);
            });
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: [],
};