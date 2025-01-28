import 'core-js/stable';
import atk from './setupAtk'; // must be the first non-vendor import
import './setupPlugins';
import './setupUtils';
import './setupFomanticUi';

__webpack_public_path__ = window.__atkBundlePublicPath + '/'; // eslint-disable-line no-undef, camelcase

export default atk; // eslint-disable-line unicorn/prefer-export-from
