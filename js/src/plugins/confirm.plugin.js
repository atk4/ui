import atkPlugin from './atk.plugin';


export default class confirm extends atkPlugin {

  main() {

    let $m = $('<div class="ui tiny modal"/>')
      .appendTo('body')
      .html(this.getDialogHtml(this.settings.title));

    let options = {};
    if (this.settings.onApprove) {
      options.onApprove = this.settings.onApprove;
    }
    if (this.settings.onDeny) {
      options.onDeny = this.settings.onDeny;
    }
    $m.data('needRemove', true).modal(options).modal('show');
  }

  getDialogHtml(message) {
    return `
          <div class=" content">${message}</div>
          <div class="actions">
            <div class="ui approve primary button">${this.settings.options.button.ok}</div>
            <div class="ui cancel button">${this.settings.options.button.cancel}</div>
           </div>
          `;
  }
}


confirm.DEFAULTS = {
  title: null,
  onApprove: null,
  onDeny: null,
  options: {button: {ok : 'Ok', cancel: 'Cancel'}}
};
