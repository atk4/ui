import $ from 'jquery';
import atkPlugin from 'plugins/atkPlugin';

export default class spinner extends atkPlugin {

    main() {
        const options = this.settings;
        // Remove any existing dimmers/spinners
        this.$el.remove('.dimmer');
        this.$el.remove('.spinner');

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

        // If replace is true we remove the existing content in the $element.
        this.showSpinner(this.$el, $finalSpinner, options.replace);

    }

    showSpinner($element, $spinner, replace = false) {
        this.settings.timer = setTimeout(() => {
            if(replace) $element.empty();
            $element.append($spinner);
        }, 500);
    }

    remove() {
        clearTimeout(this.settings.timer);
        this.$el.find('.loader').remove();
    }
}

spinner.DEFAULTS = {
    active: false,
    replace: false,
    dimmed: false,
    inline: false,
    indeterminate: false,
    loaderText: 'Loading',
    centered: false,
    baseDimmerMarkup: '<div class="ui dimmer"></div>',
    baseLoaderMarkup: '<div class="ui loader"></div>',
    timer: null,
};
