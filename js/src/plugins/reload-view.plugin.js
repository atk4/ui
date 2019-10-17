import $ from 'jquery';
import atkPlugin from './atk.plugin';
import apiService from "../services/api.service";

export default class reloadView extends atkPlugin {

    main() {
        const that  = this;

        let url = this.settings.uri;
        const userConfig = this.settings.apiConfig ? this.settings.apiConfig : {};
        const uriOptions = this.settings.uri_options ? this.settings.uri_options : {};
        // let data = {};
        let localStore = null;
        let sessionStore = null;
        let store = {};

        if (this.settings.storage) {
          localStore = atk.dataService.getLocalData(this.settings.storage);
          sessionStore = atk.dataService.getSessionData(this.settings.storage);
        }

        // merge user settings
        let settings = Object.assign({
          on: 'now',
          url: '',
          data: {},
          method: 'GET',
          obj: this.$el,
          onComplete: function(response, content) {
            if (that.settings.afterSuccess) {
              apiService.onAfterSuccess(that.settings.afterSuccess);
            }
          }
        }, userConfig);

        if (localStore) {
          store[this.settings.storage + '_local_store'] = localStore;
        }
        if (sessionStore) {
          store[this.settings.storage + '_session_store'] = sessionStore;
        }

        if (settings.method.toLowerCase() === 'post') {
          settings.url = $.atkAddParams(url, uriOptions);
          settings.data = Object.assign(settings.data, store);
        } else {
          settings.url = url;
          settings.data = Object.assign(uriOptions, store);
        }

        if(settings.url) {
            this.$el.api(settings);
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: null,
    afterSuccess: null,
    apiConfig: null,
    storage: null,
};
