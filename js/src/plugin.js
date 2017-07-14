/* https://gist.github.com/monkeymonk/c08cb040431f89f99928132ca221d647 */

/**
 * Generate a jQuery plugin
 * @param pluginName [string] Plugin name
 * @param className [object] Class of the plugin
 * @param shortHand [bool] Generate a shorthand as $.pluginName
 *
 * @example
 * import plugin from 'plugin';
 *
 * class MyPlugin {
 *     constructor(element, options) {
 *         // ...
 *     }
 * }
 *
 * MyPlugin.DEFAULTS = {};
 *
 * plugin('myPlugin', MyPlugin);
 */
export default function plugin(name, className, shortHand = false) {

    if(!$.atk){
        $.atk = new Object();
    };
    let pluginName = 'atk' + name;
    let dataName = `__${pluginName}`;
    let old = $.fn[pluginName];

    $.atk[className] = className;

    $.fn[pluginName] = function (option = {}, args = []) {

        // Check if we are calling a plugin specific function: $(element).plugin('function',[arg1, arg2]);
        if (typeof option === 'string') {
            if (this.data(dataName) && typeof this.data(dataName)[option] === 'function') {
                return this.data(dataName)['call'](option, args);
            }
        }
        return this.each(function () {
            let $this = $(this);
            let options = $.extend({}, className.DEFAULTS, typeof option === 'object' && option);
            let plugin = $this.data(dataName);

            //Create plugin and attach it to our jquery Element
            if (!plugin) {
                plugin = new $.atk[className](this, options);
                $this.data(dataName, plugin);
            }
            //Call the main function of our plugin
            plugin.main(options);
        });
    };

    // - Short hand
    if (shortHand) {
        $[pluginName] = (options) => $({})[pluginName](options);
    }
    // - No conflict
    $.fn[pluginName].noConflict = () => $.fn[pluginName] = old;
}
