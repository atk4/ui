import $ from 'jquery';

export default class spinner {
    constructor(element, options) {
        const $element = $(element);

        // Remove any existing dimmers/spinners
        $element.remove('.dimmer');
        $element.remove('.spinner');

        let $baseDimmer = $(options.baseDimmerMarkup);
        let $baseLoader = $(options.baseLoaderMarkup);

        let $finalSpinner = null;

        $baseLoader.toggleClass('active', options.active);
        $baseLoader.toggleClass('indeterminate', options.indeterminate);
        $baseLoader.toggleClass('centered', options.centered);
        $baseLoader.toggleClass('inline', options.inline);

        let isText = !!(options.loaderText);
        if(isText) {
            $baseLoader.toggleClass('text', true);
            $baseLoader.text(options.loaderText);
        }

        if(options.dimmed) {
            $baseDimmer.toggleClass('active', options.active);
            $finalSpinner = $baseDimmer.append($baseLoader);
        } else {
            $finalSpinner = $baseLoader;
        }

        this.showSpinner($element, $finalSpinner);
    }

    showSpinner($element, $spinner) {
        $spinner
            .appendTo($element);
    }
}

spinner.DEFAULTS = {
    active: true,
    dimmed: false,
    inline: true,
    indeterminate: false,
    loaderText: 'Loading',
    centered: true,
    baseDimmerMarkup: '<div class="ui dimmer"></div>',
    baseLoaderMarkup: '<div class="ui loader"></div>',
};