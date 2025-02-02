import $ from 'external/jquery';
import atk from 'atk';
import AjaxExecutePlugin from './JqueryPlugin/AjaxExecutePlugin';
import ColumnResizerPlugin from './JqueryPlugin/ColumnResizerPlugin';
import ConditionalFormPlugin from './JqueryPlugin/ConditionalFormPlugin';
import ConfirmPlugin from './JqueryPlugin/ConfirmPlugin';
import CreateModalPlugin from './JqueryPlugin/CreateModalPlugin';
import FileUploadPlugin from './JqueryPlugin/FileUploadPlugin';
import JsSearchPlugin from './JqueryPlugin/JsSearchPlugin';
import JsSortablePlugin from './JqueryPlugin/JsSortablePlugin';
import ReloadViewPlugin from './JqueryPlugin/ReloadViewPlugin';
import ScrollPlugin from './JqueryPlugin/ScrollPlugin';
import ServerSentEventPlugin from './JqueryPlugin/ServerSentEventPlugin';
import SidenavPlugin from './JqueryPlugin/SidenavPlugin';

/**
 * Register a jQuery plugin.
 *
 * @param {string}   name      Plugin name
 * @param {Function} cl        Plugin class
 * @param {boolean}  shorthand Map $.name(...) to $({}).name(...)
 */
atk.registerPlugin = function (name, cl, shorthand = false) {
    const dataName = '__' + name;

    // add plugin to atk namespace
    atk[name] = cl;

    // register plugin to jQuery fn prototype
    $.fn[name] = function (option = {}, args = []) {
        // check if we are calling a plugin specific function: $(element).plugin('function', [arg1, arg2]);
        if (typeof option === 'string') {
            return this.data(dataName).call(option, args);
        }

        return this.each(function () {
            const options = $.extend({}, cl.DEFAULTS, typeof option === 'object' && option);
            // create plugin using the constructor function store in atk namespace object
            // and add a reference of it to this jQuery object data
            $(this).data(dataName, new atk[name](this, options));
        });
    };

    if (shorthand) {
        $[name] = (options) => $({})[name](options);
    }
};

atk.registerPlugin('atkAjaxExecute', AjaxExecutePlugin);
atk.registerPlugin('atkColumnResizer', ColumnResizerPlugin);
atk.registerPlugin('atkConditionalForm', ConditionalFormPlugin);
atk.registerPlugin('atkConfirm', ConfirmPlugin, true);
atk.registerPlugin('atkCreateModal', CreateModalPlugin);
atk.registerPlugin('atkFileUpload', FileUploadPlugin);
atk.registerPlugin('atkJsSearch', JsSearchPlugin);
atk.registerPlugin('atkJsSortable', JsSortablePlugin);
atk.registerPlugin('atkReloadView', ReloadViewPlugin);
atk.registerPlugin('atkScroll', ScrollPlugin);
atk.registerPlugin('atkServerSentEvent', ServerSentEventPlugin);
atk.registerPlugin('atkSidenav', SidenavPlugin);

export default null;
