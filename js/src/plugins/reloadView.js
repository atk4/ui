import atkPlugin from 'plugins/atkPlugin';

export default class reloadView extends atkPlugin {

    main() {

        if(this.settings.uri) {
            this.$el.api({
                on: 'now',
                url: this.settings.uri,
                data: this.settings.uri_options,
                method: 'GET',
                obj: this.$el
            });
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: {},
};
