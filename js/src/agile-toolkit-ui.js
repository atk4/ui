/*global _ATKVERSION_:true */

import atk from 'atk-semantic-ui';
import 'helpers/add-params.helper';
import {plugin, createAtkplugins} from "./plugin";
import date from 'locutus/php/datetime/date';
import vueService from './services/vue.service';

// Create atk plugins.
createAtkplugins();
//add version function to atk.
atk.version = function(){return _ATKVERSION_};
//Allow to register a plugin with jQuery;
atk.registerPlugin = plugin;

atk.phpDate = date;
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
