import atkPlugin from './atk.plugin';


export default class ajaxec extends atkPlugin {

    main() {
        //Allow user to confirm if available.
        if(this.settings.confirm){
            const that = this;
            $.atkConfirm({message: this.settings.confirm, onApprove: function(){that.doExecute()}});

        } else {
            if (!this.$el.hasClass('loading')){
              this.doExecute();
            }
        }
    }

    doExecute() {
        this.$el.api({
            on: 'now',
            url: this.settings.uri,
            data: this.settings.uri_options,
            method: 'POST',
            obj: this.$el,
        });
    }
}


ajaxec.DEFAULTS = {
    uri: null,
    uri_options: {},
    confirm: null,
};
