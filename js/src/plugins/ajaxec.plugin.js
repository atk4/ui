import $ from 'external/jquery';
import atk from 'atk';
import AtkPlugin from './atk.plugin';

export default class AtkAjaxecPlugin extends AtkPlugin {
    main() {
        if (!this.settings.url) {
            console.error('Trying to execute callback without URL');

            return;
        }

        // allow user to confirm if available
        if (this.settings.confirm) {
            if (window.confirm(this.settings.confirm)) { // eslint-disable-line no-alert
                this.doExecute();
            }
        } else if (!this.$el.hasClass('loading')) {
            this.doExecute();
        }
    }

    doExecute() {
        const url = atk.urlHelper.removeAllParams(this.settings.url);
        const userConfig = this.settings.apiConfig ?? {};

        // urlOptions is always used as data in a POST request
        const data = this.settings.urlOptions ?? {};

        // retrieve param from URL
        let urlParams = atk.urlHelper.parseParams(this.settings.url);

        // get store object
        const store = atk.dataService.getStoreData(this.settings.storeName);

        const settings = {
            on: 'now',
            url: '',
            data: {},
            method: 'POST',
            ...userConfig,
        };

        if (settings.method.toUpperCase() === 'GET') {
            // set data, store and add it to URL param
            urlParams = Object.assign(urlParams, data, store);
        } else {
            settings.data = Object.assign(data, store);
        }

        settings.url = url + '?' + $.param(urlParams);
        this.$el.api(settings);
    }
}

AtkAjaxecPlugin.DEFAULTS = {
    url: null,
    urlOptions: {},
    confirm: null,
    apiConfig: null,
    storeName: null,
};
