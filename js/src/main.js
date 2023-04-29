import 'core-js/stable';
import atk from './setup-atk'; // must be the first non-vendor import
import './setup-plugins';
import './setup-utils';
import './setup-fomantic-ui';

__webpack_public_path__ = window.__atkBundlePublicPath + '/'; // eslint-disable-line no-undef, camelcase

export default atk; // eslint-disable-line unicorn/prefer-export-from
