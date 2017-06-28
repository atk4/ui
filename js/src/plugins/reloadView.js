const $ = require('jquery');

export default class reloadView {
    constructor(element, options) {
        const $element = $(element);

        $element.spinner({
            'loaderText': '',
            'active': true,
            'inline': true,
            'centered': true,
            'replace': false});

        if(options.uri) {
            $element.api({
                on: 'now',
                url: options.uri,
                data: options.uri_options,
                method: 'GET',
                obj: $element
            });
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: {},
};
