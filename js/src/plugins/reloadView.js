import atkPlugin from 'plugins/atkPlugin';
import apiService from "../services/ApiService";

export default class reloadView extends atkPlugin {

    main() {
        const that  = this;

        let settings = Object.assign({
          on: 'now',
          url: this.settings.uri,
          data: this.settings.uri_options,
          method: 'GET',
          obj: this.$el,
          onComplete: function(response, content) {
            if (that.settings.afterSuccess) {
              apiService.onAfterSuccess(that.settings.afterSuccess);
            }
          }
        }, this.settings.apiConfig);

        if(settings.url) {
            this.$el.api(settings);
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: {},
    afterSuccess: null,
    apiConfig: {},
};
