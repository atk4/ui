import $ from 'jquery';

export default class reloadView {
    constructor(element, options) {
        const $element = $(element);

        if(options.uri) {
            $.get(options.uri, options.uri_options, (data) => {
                if(options.replace) {
                    $element.replaceWith(data);
                } else {
                    $element.html(data);
                }

                options.complete.call(this);
            });
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: {},
    replace: true,
    complete: () => {}
};