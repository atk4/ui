import $ from 'jquery';

import registerPlugin from './plugin';

// Import our plugins
import reloadView from 'plugins/reloadView'

// Register our plugins
registerPlugin('reloadView', reloadView);