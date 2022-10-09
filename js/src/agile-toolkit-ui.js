import 'core-js/stable';
import atkFomantic from './atk-fomantic-ui';
import { registerPlugin } from './plugin';
import { tableDropdown } from './helpers/table-dropdown.helper';
import {
    atkOptions, atkEventBus, atkUtils, atkDebounce,
} from './atk-utils';
import dataService from './services/data.service';
import panelService from './services/panel.service';
import vueService from './services/vue.service';
import popupService from './services/popup.service';

__webpack_public_path__ = window.__atkBundlePublicPath + '/'; // eslint-disable-line no-undef, camelcase, no-underscore-dangle

const atk = { ...atkFomantic };

atk.options = atkOptions;
atk.eventBus = atkEventBus;
atk.utils = atkUtils;

atk.debounce = atkDebounce;

// Allow to register a plugin with jQuery;
atk.registerPlugin = registerPlugin;

atk.dataService = dataService;
atk.panelService = panelService;
atk.tableDropdown = tableDropdown;
atk.vueService = vueService;
atk.popupService = popupService;

/**
 * Exporting services in order to be available globally
 * or by importing it into your own module.
 *
 * Available as a global Var: atk.uploadService.uploadFiles()
 * Available as an import:
 * import atk from atk4JS;
 * atk.uploadService.uploadFiles();
 */
export default atk;
