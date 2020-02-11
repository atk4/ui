import $ from 'jquery';
import atkPlugin from './atk.plugin';
import apiService from "../services/api.service";

/**
 * Reload a view using Fomantic-ui API.
 * Prefer method is GET.
 *
 * You can include WebStorage value within the request
 * by setting the store name (key) value.
 * When a store value is requested, it will be add
 * to the urlParameter for GET method but will be included in formData
 * for POST method.
 */
export default class reloadView extends atkPlugin {

    main() {

        if (!this.settings.uri) {
          console.error('Trying to reload view without url.');
          return;
        }

        const that  = this;
        const url = $.atk.getUrl(this.settings.uri);
        const userConfig = this.settings.apiConfig ? this.settings.apiConfig : {};

        // add new param and remove duplicate, prioritizing the latest one.
        let urlParam = Object.assign($.atkGetQueryParam(decodeURIComponent(this.settings.uri)), this.settings.uri_options ? this.settings.uri_options : {});

        // get store object.
        let store = atk.dataService.getStoreData(this.settings.storeName);

        // merge user settings
        let settings = Object.assign({
          on: 'now',
          url: '',
          data: {},
          method: 'GET',
          onComplete: function(response, content) {
            if (that.settings.afterSuccess) {
              apiService.onAfterSuccess(that.settings.afterSuccess);
            }
          }
        }, userConfig);

        // if post then we need to set our store into settings data.
        if (settings.method.toLowerCase() === 'post') {
          settings.data = Object.assign(settings.data, store);
        } else {
          urlParam = Object.assign(urlParam, store);
        }

        settings.url = url + '?' + $.param(urlParam);
        this.$el.api(settings);
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: null,
    afterSuccess: null,
    apiConfig: null,
    storeName: null,
};
