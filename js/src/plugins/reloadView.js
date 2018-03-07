import atkPlugin from 'plugins/atkPlugin';
import apiService from "../services/ApiService";

export default class reloadView extends atkPlugin {

    main() {

        if(this.settings.uri) {
            const that  = this;
            this.$el.api({
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
            });
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: {},
    afterSuccess: null,
};
