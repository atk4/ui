import $ from 'jquery';

import {registerMethod, registerPlugin} from './plugin';

// Import our plugins
import spinner from 'plugins/spinner'
import reloadView from 'plugins/reloadView'
import modal from 'plugins/modal'

// Register our plugins
registerPlugin('spinner', spinner);
registerPlugin('reloadView', reloadView);

registerMethod('modal', 'ATK', modal);