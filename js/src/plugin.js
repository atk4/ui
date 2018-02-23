import $ from 'jquery';
import spinner from "./plugins/spinner";
import serverEvent from "./plugins/serverEvent";
import reloadView from "./plugins/reloadView";
import ajaxec from "./plugins/ajaxec";
import createModal from "./plugins/createModal";
import notify from "./plugins/notify";
import fileUpload from "./plugins/fileUpload";
import jsSearch from "./plugins/jsSearch";

/**
 * Generate a jQuery plugin
 * @param name [string] Plugin name
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
 *
 * credit : https://gist.github.com/monkeymonk/c08cb040431f89f99928132ca221d647
 *
 * import $ from 'jquery' will bind '$' var to jQuery var without '$' var conflicting with other library
 * in final webpack output.
 */

//Create plugins.
plugin('Spinner', spinner);
plugin('ReloadView', reloadView);
plugin('Ajaxec', ajaxec);
plugin('CreateModal', createModal);
plugin('Notify', notify, true);
plugin('ServerEvent', serverEvent, true);
plugin('FileUpload', fileUpload);
plugin('JsSearch', jsSearch);

export default function plugin(name, className, shortHand = false) {
        // Add atk namespace to jQuery global space.
        if(!$.atk){
            $.atk = new Object();
        };

        let pluginName = 'atk' + name;
        let dataName = `__${pluginName}`;
        let old = $.fn[pluginName];

        // add plugin to atk namespace.
        $.atk[name] = className;

        // register plugin to jQuery fn prototype.
        $.fn[pluginName] = function (option = {}, args = []) {

            // Check if we are calling a plugin specific function: $(element).plugin('function',[arg1, arg2]);
            if (typeof option === 'string') {
                if (this.data(dataName) && typeof this.data(dataName)[option] === 'function') {
                    return this.data(dataName)['call'](option, args);
                }
            }

            return this.each(function () {
                let options = $.extend({}, className.DEFAULTS, typeof option === 'object' && option);
                // create plugin using the constructor function store in atk namespace object
                // and add a reference of it to this jQuery object data.
                $(this).data(dataName, new $.atk[name](this, options));
            });
        };

        // - Short hand
        if (shortHand) {
            $[pluginName] = (options) => $({})[pluginName](options);
        }
        // - No conflict
        $.fn[pluginName].noConflict = () => $.fn[pluginName] = old;
}
