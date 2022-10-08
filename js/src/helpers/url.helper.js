/**
 * Url helper jQuery functions.
 *
 * - AddParams - Pass an url with an object and object key=value pair will be
 *   added to the url as get parameter.
 *   ex: $.atkAddParams('myurl.php', {q: 'test', 'reload': 'my_view'})
 *   will return: myurl.php?q=test&reload=my_view
 *
 * - RemoveParam - remove a parameter from an url string.
 *   ex: $.atkRemoveParam('myurl.php?q=test&reload=my_view', 'q')
 *   will return: myurl.php?reload=my_view
 */

(function ($) {
    if (!$.atk) {
        $.atk = {};
    }

    /**
     * Get the base url from string.
     *
     * @param url
     * @returns {*|string}
     */
    $.atk.getUrl = function (url) {
        return url.split('?')[0];
    };

    /**
     * Get each url query parameter as a key:value pair object.
     *
     * @param str
     * @returns {{}|unknown}
     */
    $.atk.getQueryParams = function (str) {
        if (str.split('?')[1]) {
            return decodeURIComponent(str.split('?')[1])
                .split('&')
                .reduce((obj, unsplitArg) => {
                    const arg = unsplitArg.split('=');
                    obj[arg[0]] = arg[1]; // eslint-disable-line prefer-destructuring

                    return obj;
                }, {});
        }

        return {};
    };

    /**
     * Add param to an url string.
     *
     * @param url
     * @param data
     * @returns {*}
     */
    $.atk.addParams = function (url, data) {
        if (!$.isEmptyObject(data)) {
            url += (url.indexOf('?') >= 0 ? '&' : '?') + $.param(data);
        }

        return url;
    };

    /**
     * Remove param from an url string.
     *
     * @param url
     * @param param
     * @returns {string|*|string}
     */
    $.atk.removeParam = function (url, param) {
        const splitUrl = url.split('?');
        if (splitUrl.length === 0) {
            return url;
        }

        const urlBase = splitUrl[0];
        if (splitUrl.length === 1) {
            return urlBase;
        }

        const newParams = splitUrl[1].split('&').filter((item) => item.split('=')[0] !== param);
        if (newParams.length > 0) {
            return urlBase + '?' + newParams.join('&');
        }

        return urlBase;
    };
}(jQuery));

export default (function ($) {
    $.atkGetUrl = $.atk.getUrl;
    $.atkAddParams = $.atk.addParams;
    $.atkRemoveParam = $.atk.removeParam;
    $.atkGetQueryParam = $.atk.getQueryParams;
}(jQuery));
