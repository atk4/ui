import $ from 'jquery';

/* eslint-disable jsdoc/require-param-type */

/**
 * Base implementation of jQuery plugin in Agile Toolkit.
 */
export default class atkPlugin {
    /**
     * Default plugin constructor
     *
     * @returns {atkPlugin}
     */
    constructor(element, options) {
        this.$el = $(element);
        this.settings = options;
        this.main();
    }

    /**
     * The main plugin method. This is the method call by default
     * when invoking the plugin on a jQuery element.
     * $(selector).pluginName({});
     * The plugin should normally override this class.
     */
    main() {}

    /**
     * Call a plugin method via the initializer function.
     * Simply call the method like: $(selector).pluginName('method', [arg1, arg2])
     *
     * @param       fn   string representing the method name to execute.
     * @param       args array of arguments need for the method to execute.
     * @returns {*}
     */
    call(fn, args) {
        return this[fn](...args);
    }
}
