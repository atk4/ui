// Add atk namespace to jQuery global space.
if(!$.atk){
    $.atk = new Object();
};

$.atk['addParams'] = function (url, data) {
    if (!$.isEmptyObject(data)) {
        url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + $.param(data);
    }

    return url;
}

export default (function($){
    $.atkAddParams = $.atk.addParams
})(jQuery);


