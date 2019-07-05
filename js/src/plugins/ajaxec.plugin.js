import atkPlugin from './atk.plugin';


export default class ajaxec extends atkPlugin {

    main() {
        //Allow user to confirm if available.
        if(this.settings.confirm){
            const that = this;
            $.atkConfirm({title: this.settings.confirm, onApprove: function(){that.doExecute()}})
            // const that = this;
            // let $m = $('<div class="ui tiny modal"/>')
            //   .appendTo('body')
            //   .html(this.getDialogHtml(this.settings.confirm));
            //
            // $m.modal({onApprove: function(){that.doExecute()}}).modal('show');

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
