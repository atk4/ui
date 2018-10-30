import $ from 'jquery';
import spinner from "./plugins/spinner";
import serverEvent from "./plugins/serverEvent";
import reloadView from "./plugins/reloadView";
import ajaxec from "./plugins/ajaxec";
import createModal from "./plugins/createModal";
import notify from "./plugins/notify";
import fileUpload from "./plugins/fileUpload";
import jsSearch from "./plugins/jsSearch";
import jsSortable from "./plugins/jsSortable";
import conditionalForm from "./plugins/conditionalForm";
import columnResizer from "./plugins/columnResizer";
import scroll from "./plugins/scroll";

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
function plugin(name, className, shortHand = false) {
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

/**
 * Create all jQuery plugins need for atk.
 */
function createAtkplugins() {
  const atkJqPlugins = [
    {name: 'Spinner', plugin: spinner, sh: false},
    {name: 'ReloadView', plugin: reloadView, sh: false},
    {name: 'Ajaxec', plugin: ajaxec, sh: false},
    {name: 'CreateModal', plugin: createModal, sh: false},
    {name: 'Notify', plugin: notify,sh: true},
    {name: 'ServerEvent', plugin: serverEvent, sh: true},
    {name: 'FileUpload', plugin: fileUpload, sh: false},
    {name: 'JsSearch', plugin: jsSearch, sh: false},
    {name: 'JsSortable', plugin: jsSortable, sh: false},
    {name: 'ConditionalForm', plugin: conditionalForm, sh: true},
    {name: 'ColumnResizer', plugin: columnResizer, sh: false},
    {name: 'Scroll', plugin: scroll, sh: false},
  ];

  atkJqPlugins.forEach((atkJqPlugin) => {
    plugin(atkJqPlugin.name, atkJqPlugin.plugin, atkJqPlugin.sh);
  });
}

export {plugin, createAtkplugins};
