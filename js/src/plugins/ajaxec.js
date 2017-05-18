import $ from 'jquery';

export default class ajaxec {
    constructor(element, options) {
        const $element = $(element);

        // ask for user confirmation just before
        // TODO: because this is constructor, button can't be clicked again :(
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
