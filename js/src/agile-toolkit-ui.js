/*global _ATKVERSION_:true */

import atk from 'atk-semantic-ui';
import 'helpers/url.helper';
import {tableDropdown} from "./helpers/table-dropdown.helper";
import {plugin, createAtkplugins} from "./plugin";
import date from 'locutus/php/datetime/date';
import vueService from './services/vue.service';
import dataService from "./services/data.service";
import panelService from "./services/panel.service";

// Create atk plugins.
createAtkplugins();
//add version function to atk.
atk.version = function(){return _ATKVERSION_};
//Allow to register a plugin with jQuery;
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
