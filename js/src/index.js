import $ from 'jquery';

import registerPlugin from './plugin';

// Import our plugins
import spinner from 'plugins/spinner'
import reloadView from 'plugins/reloadView'
import ajaxec from 'plugins/ajaxec'

// Register our plugins
registerPlugin('spinner', spinner);
registerPlugin('reloadView', reloadView);
registerPlugin('ajaxec', ajaxec);
