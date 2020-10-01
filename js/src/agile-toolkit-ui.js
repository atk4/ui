/* eslint-disable */
/* global _ATKVERSION_:true, __webpack_public_path__:true */
__webpack_public_path__ = window.__atkBundlePublicPath === undefined ? '/public/' :  window.__atkBundlePublicPath + '/';

import debounce from 'debounce';
import 'core-js/stable';
import atkSemantic from 'atk-semantic-ui';
import date from 'locutus/php/datetime/date';
import { tableDropdown } from './helpers/table-dropdown.helper';
import { plugin } from './plugin';
import { atkOptions, atkEventBus, atkUtils } from './atk-utils';
import dataService from './services/data.service';
import panelService from './services/panel.service';
import vueService from './services/vue.service';

const atk = { ...atkSemantic };

// add version function to atk.
atk.version = () => _ATKVERSION_;
atk.options = atkOptions;
atk.eventBus = atkEventBus;
atk.utils = atkUtils;

atk.debounce = (fn, value) => {
    const timeOut = atk.options.get('debounceTimeout');
    return debounce(fn, timeOut !== null ? timeOut : value);
};

// Allow to register a plugin with jQuery;
atk.registerPlugin = plugin;

atk.phpDate = date;
atk.dataService = dataService;
atk.panelService = panelService;
atk.tableDropdown = tableDropdown;
atk.vueService = vueService;

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
