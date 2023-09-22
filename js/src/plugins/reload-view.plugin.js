import $ from 'external/jquery';
import atk from 'atk';
import AtkPlugin from './atk.plugin';

/**
 * Reload a view from server. Default request method is GET.
 *
 * You can include WebStorage value within the request
 * by setting the store name (key) value.
 * When a store value is requested, it will be add
 * to the urlParameter for GET method but will be included in formData
 * for POST method.
 */
export default class AtkReloadViewPlugin extends AtkPlugin {
    main() {
        if (!this.settings.url) {
            console.error('Trying to reload view without URL');

            return;
        }

        const url = atk.urlHelper.removeAllParams(this.settings.url);
        const userConfig = this.settings.apiConfig ?? {};

        // add new param and remove duplicate, prioritizing the latest one
        let urlParams = Object.assign(
            atk.urlHelper.parseParams(this.settings.url),
            this.settings.urlOptions ?? {}
        );

        // get store object
        const store = atk.dataService.getStoreData(this.settings.storeName);

        // merge user settings
        const settings = {
            on: 'now',
            url: '',
            data: {},
            method: 'GET',
            onComplete: (response, content) => {
                if (this.settings.afterSuccess) {
                    atk.apiService.onAfterSuccess(this.settings.afterSuccess);
                }
            },
            ...userConfig,
        };

        // if post then we need to set our store into settings data
        if (settings.method.toUpperCase() === 'POST') {
            settings.data = Object.assign(settings.data, store);
        } else {
            urlParams = Object.assign(urlParams, store);
        }

        settings.url = url + '?' + $.param(urlParams);

        this.$el.api(settings);
    }
}

AtkReloadViewPlugin.DEFAULTS = {
    url: null,
    urlOptions: null,
    afterSuccess: null,
    apiConfig: null,
    storeName: null,
};
