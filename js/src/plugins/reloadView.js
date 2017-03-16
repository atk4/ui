import $ from 'jquery';

export default class reloadView {
    constructor(element, options) {
        const $element = $(element);

        $element
            .text('')
            .append("<div class='ui active loader inline'></div>");

        $.get(options.callback, function(data) {
            $element.replaceWith(data);
        });
    }
}

