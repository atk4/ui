import $ from 'external/jquery';
import atk from 'atk';
import AtkPlugin from './atk.plugin';
import apiService from '../services/api.service';

/**
 * Reload a view using Fomantic-UI API.
 * Prefer method is GET.
 *
 * You can include WebStorage value within the request
 * by setting the store name (key) value.
 * When a store value is requested, it will be add
 * to the urlParameter for GET method but will be included in formData
 * for POST method.
 */
export default class AtkReloadViewPlugin extends AtkPlugin {
    main() {
        if (!this.settings.uri) {
            console.error('Trying to reload view without url.');

            return;
        }

        const url = atk.removeAllUrlParams(this.settings.uri);
        const userConfig = this.settings.apiConfig ? this.settings.apiConfig : {};

        // add new param and remove duplicate, prioritizing the latest one.
        let urlParams = Object.assign(
            atk.parseUrlParams(this.settings.uri),
            this.settings.uriOptions ? this.settings.uriOptions : {},
        );

        // get store object.
        const store = atk.dataService.getStoreData(this.settings.storeName);

        // merge user settings
        const settings = {
            on: 'now',
            url: '',
            data: {},
            method: 'GET',
            onComplete: (response, content) => {
                if (this.settings.afterSuccess) {
                    apiService.onAfterSuccess(this.settings.afterSuccess);
                }
            },
            ...userConfig,
        };

        // if post then we need to set our store into settings data.
        if (settings.method.toLowerCase() === 'post') {
            settings.data = Object.assign(settings.data, store);
        } else {
            urlParams = Object.assign(urlParams, store);
        }

        settings.url = url + '?' + $.param(urlParams);
        this.$el.api(settings);
    }
}

AtkReloadViewPlugin.DEFAULTS = {
    uri: null,
    uriOptions: null,
    afterSuccess: null,
    apiConfig: null,
    storeName: null,
};
