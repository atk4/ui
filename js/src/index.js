import $ from 'jquery';

import registerPlugin from './plugin';

// Import our plugins
import spinner from 'plugins/spinner'
import reloadView from 'plugins/reloadView'

// Register our plugins
registerPlugin('spinner', spinner);
registerPlugin('reloadView', reloadView);