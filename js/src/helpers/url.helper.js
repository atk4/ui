/**
 *  Url helper jQuery functions.
 *
 * - AddParams - Pass an url with an object and object key=value pair will be
 *   added to the url as get parameter.
 *   ex: $.atkAddParams('myurl.php', {q: 'test', 'reload': 'my_view'})
 *   will return: myurl.php?q=test&reload=my_view
 *
 * -RemoveParam - remove a parameter from an url string.
 *  ex: $.atkRemoveParam('myurl.php?q=test&reload=my_view', 'q')
 *  will return: myurl.php?reload=my_view
 *
 */

(function($) {
    if(!$.atk){
        $.atk = new Object();
    };

    $.atk['addParams'] = function (url, data) {
        if (!$.isEmptyObject(data)) {
            url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + $.param(data);
        }

        return url;
    };

    $.atk['removeParam'] = function (url, param) {
        let newUrl;

        const splitUrl = url.split('?');
        if (splitUrl.length === 0) {
            return url;
        }

        const urlBase = splitUrl[0];
        if (splitUrl.length === 1) {
            return urlBase;
        }

        const newParams = splitUrl[1].split('&').filter(item => item.split('=')[0] != param);
        if (newParams.length > 0) {
            return urlBase+'?'+newParams.join('&');
        } else {
            return urlBase;
        }
    };

})(jQuery);


export default (function($){
    $.atkAddParams = $.atk.addParams;
    $.atkRemoveParam = $.atk.removeParam;
})(jQuery);

