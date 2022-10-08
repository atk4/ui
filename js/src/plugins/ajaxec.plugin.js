import $ from 'jquery';
import atkPlugin from './atk.plugin';

export default class ajaxec extends atkPlugin {
    main() {
        if (!this.settings.uri) {
            console.error('Trying to execute callback without url.');

            return;
        }

        // Allow user to confirm if available.
        if (this.settings.confirm) {
            if (window.confirm(this.settings.confirm)) { // eslint-disable-line no-alert
                this.doExecute();
            }
        } else if (!this.$el.hasClass('loading')) {
            this.doExecute();
        }
    }

    doExecute() {
        const url = $.atk.getUrl(this.settings.uri);
        const userConfig = this.settings.apiConfig ? this.settings.apiConfig : {};

        // uri_options is always use as data in a post request.
        const data = this.settings.uri_options ? this.settings.uri_options : {};

        // retrieve param from url.
        let urlParam = $.atkGetQueryParam(this.settings.uri);

        // get store object.
        const store = atk.dataService.getStoreData(this.settings.storeName);

        const settings = {
            on: 'now',
            url: '',
            data: {},
            method: 'POST',
            ...userConfig,
        };

        if (settings.method.toLowerCase() === 'get') {
            // set data, store and add it to url param.
            urlParam = Object.assign(urlParam, data, store);
        } else {
            settings.data = Object.assign(data, store);
        }

        settings.url = url + '?' + $.param(urlParam);
        this.$el.api(settings);
    }
}

ajaxec.DEFAULTS = {
    uri: null,
    uri_options: {},
    confirm: null,
    apiConfig: null,
    storeName: null,
};
