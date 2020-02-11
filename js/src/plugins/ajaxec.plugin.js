import atkPlugin from './atk.plugin';


export default class ajaxec extends atkPlugin {

    main() {
        if (!this.settings.uri) {
            console.error('Trying to execute callback without url.');
            return;
        }

        //Allow user to confirm if available.
        if(this.settings.confirm){
            if(confirm(this.settings.confirm)) {
                this.doExecute();
            }
        } else {
            if (!this.$el.hasClass('loading')){
                this.doExecute();
            }
        }
    }

    doExecute() {

        // userConfig callback can use that in order to refer to this plugin.
        const that = this;
        const url = $.atk.getUrl(this.settings.uri);
        const userConfig = this.settings.apiConfig ? this.settings.apiConfig : {};

        // uri_options is always use as data in a post request.
        const data = this.settings.uri_options ? this.settings.uri_options : {};

        // retrieve param from url.
        let urlParam = $.atkGetQueryParam(decodeURIComponent(this.settings.uri));

        // get store object.
        let store = atk.dataService.getStoreData(this.settings.storeName);

        let settings = Object.assign({
            on: 'now',
            url: '',
            data: {},
            method: 'POST',
        }, userConfig);

        if (settings.method.toLowerCase() === 'get') {
            // set data, store and add it to url param.
            urlParam = Object.assign(urlParam, data, store);
        } else {
            settings.data = Object.assign(data, store);
        }

        settings.url = url + '?' + $.param(urlParam);
        this.$el.api(settings);
    }
}


ajaxec.DEFAULTS = {
    uri: null,
    uri_options: {},
    confirm: null,
    apiConfig: null,
    storeName: null,
};
