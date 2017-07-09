
export default class ajaxec {
    constructor(element, options) {
        const $element = $(element);

        // ask for user confirmation just before
        if (options.confirm) {
            if (!confirm(options.confirm)) {
                return ;
            }
        }

        $element.api({
            on: 'now',
            url: options.uri,
            data: options.uri_options,
            method: 'POST',
            obj: $element
        });
    }
}

ajaxec.DEFAULTS = {
    uri: null,
    uri_options: {},
    confirm: null,
};
