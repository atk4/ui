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

        // workaround Fomantic-UI modal is hidden when "loading" class is set by
        // https://github.com/fomantic/Fomantic-UI/blob/2.9.3/src/definitions/behaviors/api.js#L524
        // because of
        // https://github.com/fomantic/Fomantic-UI/blob/2.9.3/src/definitions/modules/modal.less#L396
        // https://github.com/fomantic/Fomantic-UI/blob/2.9.3/src/definitions/modules/transition.less#L44
        // related fix https://github.com/fomantic/Fomantic-UI/pull/2982
        if (!settings.stateContext && this.$el.hasClass('ui modal') && this.$el.children().length > 0 /* prevent loading in original DOM location */) {
            [settings.stateContext] = this.$el.children('.content');
            if (!settings.className) {
                settings.className = [];
            }
            settings.className.loading = 'ui basic fitted segment loading atk-hide-loading-content';
        }
        // and for our panel until migrated
        // https://github.com/atk4/ui/issues/1812#issuecomment-1273092181
        if (!settings.stateContext && this.$el.hasClass('atk-right-panel') && this.$el.children().length > 0 /* prevent loading in original DOM location */) {
            [settings.stateContext] = this.$el.children('.ui.segment:not(:has(> .atk-panel-warning))');
            if (!settings.className) {
                settings.className = [];
            }
            settings.className.loading = 'loading atk-hide-loading-content';
        }

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
