import atk from 'atk-semantic-ui';
import 'helpers/addParams';
import registerPlugin from './plugin';

atk.version = function(){return _ATKVERSION_}
//Allow to register a plugin with jQuery;
atk.registerPlugin = registerPlugin;


/**
 * Exporting services in order to be available globally
 * or by importing it into your own module.
 *
 * Available as a global Var: atk.uploadService.fileUpload()
 * Available as an import:
 *  import atk from atk4JS;
 *  atk.uploadService.fileUpload();
 */
module.exports = atk;
