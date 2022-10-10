import $ from 'external/jquery';

export default {
    /**
     * Get each URL query parameter as a key:value pair object.
     *
     * @returns {object}
     */
    parseParams: function (str) {
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
    },

    /**
     * Add param to an URL string.
     *
     * ex: atk.urlHelper.appendParams('myurl.php', { q: 'test', 'reload': 'myView' })
     * will return: myurl.php?q=test&reload=myView
     *
     * @returns {string}
     */
    appendParams: function (url, data) {
        if (!$.isEmptyObject(data)) {
            url += (url.indexOf('?') >= 0 ? '&' : '?') + $.param(data);
        }

        return url;
    },

    /**
     * Remove param from an URL string.
     *
     * ex: atk.urlHelper.removeParam('myurl.php?q=test&reload=myView', 'q')
     * will return: myurl.php?reload=myView
     *
     * @returns {string}
     */
    removeParam: function (url, param) {
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
    },

    /**
     * Remove whole query string from an URL string.
     *
     * @returns {string}
     */
    removeAllParams: function (url) {
        return url.split('?')[0];
    },
};
