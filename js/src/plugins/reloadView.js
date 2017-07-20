import atkPlugin from 'plugins/atkPlugin';

export default class reloadView extends atkPlugin {

    main() {
        // const spinner = this.$el.atkSpinner({
        //     'loaderText': '',
        //     'active': true,
        //     'inline': true,
        //     'centered': true,
        //     'replace': false});

        if(this.settings.uri) {
            this.$el.api({
                on: 'now',
                url: this.settings.uri,
                data: this.settings.uri_options,
                method: 'GET',
                obj: this.$el
                // onComplete: function(response, content) {
                //     content.atkSpinner('remove');
                // }
            });
        }
    }
}

reloadView.DEFAULTS = {
    uri: null,
    uri_options: {},
};
