import $ from 'jquery';

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
registerPlugin('addParams', addParams);
registerPlugin('createModal', createModal);

$.addParams = function ( url, data )
{
    if ( ! $.isEmptyObject(data) )
    {
        url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + $.param(data);
    }

    return url;
}
