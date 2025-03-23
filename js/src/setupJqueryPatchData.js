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

function initAttributeData(elem) {
    if (elem.nodeType === 1 && elem[jqueryDataExpandoName] === undefined) {
        for (const attribute of elem.attributes) {
            if (attribute.name.startsWith('data-')) {
                $.data(elem, attribute.name.slice(5), attribute.value);
            }
        }
    }
}

const jqueryFnDataFxOrig = $.fn.data;
$.fn.data = function (key, value) {
    this.each(function () {
        initAttributeData(this);
    });

    return jqueryFnDataFxOrig.apply(this, arguments); // eslint-disable-line unicorn/prefer-reflect-apply, prefer-rest-params
};

export default null;
