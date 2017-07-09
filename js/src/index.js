import 'atk4-semantic-ui';
import registerPlugin from './plugin';

// Import our plugins
import spinner from 'plugins/spinner'
import reloadView from 'plugins/reloadView'
import ajaxec from 'plugins/ajaxec'
import addParams from 'plugins/addParams'
import createModal from 'plugins/createModal'

// Register our plugins
registerPlugin('spinner', spinner);
registerPlugin('reloadView', reloadView);
registerPlugin('ajaxec', ajaxec);
registerPlugin('addParams', addParams, true);
registerPlugin('createModal', createModal);
