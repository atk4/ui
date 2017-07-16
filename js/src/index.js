import 'atk4-semantic-ui';
import 'helpers/addParams';

import registerPlugin from './plugin';

// Import our plugins
import spinner from 'plugins/spinner'
import reloadView from 'plugins/reloadView'
import ajaxec from 'plugins/ajaxec'
import createModal from 'plugins/createModal'

// Register our plugins
registerPlugin('spinner', spinner);
registerPlugin('reloadView', reloadView);
registerPlugin('ajaxec', ajaxec);
registerPlugin('createModal', createModal);
