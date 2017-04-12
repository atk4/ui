import $ from 'jquery';

export default class addParams {
    constructor(element, options) {
        var url = options.url;
        if ( ! $.isEmptyObject(options.params) )
        {
            url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + $.param(options.params);
        }

        return url;
    }
}

addParams.DEFAULTS = {
    url: null,
    params: {},
}
