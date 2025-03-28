import $ from 'external/jquery';

// enforce all $elem.data() initialized from HTML attributes to be of type string
// https://github.com/jquery/jquery/blob/3.7.1/src/data.js#L24-L47

// https://github.com/jquery/jquery/blob/3.7.1/src/data/Data.js#L11
// https://github.com/jquery/jquery/blob/3.7.1/src/data/var/dataUser.js#L6
const jqueryDataExpandoName = (function () {
    const dummyElem = document.createElement('div');
    const keysBefore = Object.keys(dummyElem);
    $(dummyElem).data('x', true);
    const keysAfter = Object.keys(dummyElem);
    const keysDiff = keysAfter.filter((v) => !keysBefore.includes(v));
    if (keysDiff.length !== 1 || !/^jQuery\d+$/.test(keysDiff[0])) {
        throw new Error('Failed to discover jQuery "data expando name"');
    }

    return keysDiff[0];
}());

// https://github.com/jquery/jquery/blob/3.7.1/src/data/Data.js#L58
// https://github.com/jquery/jquery/blob/3.7.1/src/core/camelCase.js
function jqueryCamelCase(value) {
    return value.replace(/^-ms-/, 'ms-').replaceAll(/-[a-z]/g, (v) => v.slice(1).toUpperCase());
}

function initAttributeData(elem, key) {
    if (key !== undefined && elem[jqueryDataExpandoName] === undefined) {
        key = undefined;
    }

    if (elem.nodeType === 1 && (elem[jqueryDataExpandoName] === undefined || key !== undefined)) {
        for (const attribute of elem.attributes) {
            if (attribute.name.startsWith('data-')) {
                const kCamel = jqueryCamelCase(attribute.name.slice(5));
                if (key === undefined || (kCamel === jqueryCamelCase(key) && $.data(elem, kCamel) === undefined)) {
                    $.data(elem, kCamel, attribute.value);
                }
            }
        }
    }
}

const jqueryFnDataFxOrig = $.fn.data;
$.fn.data = function (key, value) {
    this.each(function () {
        if (key === undefined || typeof key === 'string') {
            initAttributeData(this, key);
        } else {
            for (const k of Object.keys(key)) {
                initAttributeData(this, k);
            }
        }
    });

    return jqueryFnDataFxOrig.apply(this, arguments); // eslint-disable-line unicorn/prefer-reflect-apply, prefer-rest-params
};

export default null;
