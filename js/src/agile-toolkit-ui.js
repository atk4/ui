/* global _ATKVERSION_:true */

import debounce from 'debounce';
import 'core-js/stable';
import atk from 'atk-semantic-ui';
import 'helpers/url.helper';
import date from 'locutus/php/datetime/date';
import { tableDropdown } from './helpers/table-dropdown.helper';
import { plugin, createAtkplugins } from './plugin';
import vueService from './services/vue.service';
import dataService from './services/data.service';
import panelService from './services/panel.service';

// Create atk plugins.
createAtkplugins();
// add version function to atk.
atk.version = () => _ATKVERSION_;

// Use closure for atk options property in order to keep them private.
atk.options = (function () {
    const options = {
        // Value for debounce time out (in ms) that will be apply globally when set.
        debounceTimeout: null,
    };
    return {
        setOption: (name, value) => { options[name] = value; },
        getOption: (name) => options[name],
    };
}());

atk.debounce = (fn, value) => {
    const timeOut = atk.options.getOption('debounceTimeout');
    return debounce(fn, timeOut !== null ? timeOut : value);
};

// Allow to register a plugin with jQuery;
atk.registerPlugin = plugin;

atk.phpDate = date;
atk.vueService = vueService;
atk.dataService = dataService;
atk.panelService = panelService;
atk.tableDropdown = tableDropdown;

/**
 * Exporting services in order to be available globally
 * or by importing it into your own module.
 *
 * Available as a global Var: atk.uploadService.fileUpload()
 * Available as an import:
 *  import atk from atk4JS;
 *  atk.uploadService.fileUpload();
 */
export default atk;
