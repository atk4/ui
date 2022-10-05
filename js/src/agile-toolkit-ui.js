import 'core-js/stable';
import atkFomantic from 'atk-fomantic-ui';
import date from 'locutus/php/datetime/date';
import { tableDropdown } from './helpers/table-dropdown.helper';
import { plugin } from './plugin';
import {
    atkOptions, atkEventBus, atkUtils, atkDebounce,
} from './atk-utils';
import dataService from './services/data.service';
import panelService from './services/panel.service';
import vueService from './services/vue.service';
import popupService from './services/popup.service';

/* eslint-disable */
/* global __webpack_public_path__:true */
__webpack_public_path__ = window.__atkBundlePublicPath === undefined ? '/public/' :  window.__atkBundlePublicPath + '/';
/* eslint-enable */

const atk = { ...atkFomantic };

atk.options = atkOptions;
atk.eventBus = atkEventBus;
atk.utils = atkUtils;

atk.debounce = atkDebounce;

// Allow to register a plugin with jQuery;
atk.registerPlugin = plugin;

atk.phpDate = date;
atk.dataService = dataService;
atk.panelService = panelService;
atk.tableDropdown = tableDropdown;
atk.vueService = vueService;
atk.popupService = popupService;

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
