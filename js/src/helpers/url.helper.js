import $ from 'external/jquery';

export default {
    /**
     * Get each URL query parameter as a key:value pair object.
     *
     * @returns {object}
     */
    parseParams: function (url) {
        const query = url.includes('?') ? url.substring(url.indexOf('?') + 1) : '';

        return (query.length > 0 ? query.split('&') : [])
            .reduce((obj, queryPart) => {
                let k = queryPart;
                let v = null;
                if (k.includes('=')) {
                    v = k.substring(k.indexOf('=') + 1);
                    k = k.substring(0, k.indexOf('='));
                }

                obj[decodeURIComponent(k)] = decodeURIComponent(v);

                return obj;
            }, {});
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
        const query = $.param(data);
        if (query !== '') {
            url += (url.includes('?') ? '&' : '?') + query;
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
        const query = url.includes('?') ? url.substring(url.indexOf('?') + 1) : '';
        const newParams = (query.length > 0 ? query.split('&') : [])
            .filter((queryPart) => decodeURIComponent(queryPart.split('=')[0]) !== param);

        return url.substring(0, url.indexOf('?'))
                + (newParams.length > 0 ? '?' + newParams.join('&') : '');
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
