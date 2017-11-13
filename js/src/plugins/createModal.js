import atkPlugin from 'plugins/atkPlugin';
import $ from 'jquery';

export default class createModal extends atkPlugin {

  main() {
      const options = this.settings;
      // make sure we have an object when no option is passed
      if ($.isArray(options.uri_options)) {
          options.uri_options = {};
      }
      // create modal and add it to the DOM
      let $m = $('<div class="atk-modal ui modal scrolling"/>')
          .appendTo('body')
          .html(this.getDialogHtml(options.title));

      //add setting to our modal for modalService
      $m.data({uri:options.uri, type:options.mode, args:options.uri_options, needRemove:true, needCloseTrigger:true, label: options.label});

      //call semantic-ui modal
      $m.modal(options.modal).modal('show');
  }

  getDialogHtml(title) {
    return `<i class="close icon"></i>
          <div class="header">${title}</div>
          <div class="image content atk-dialog-content">
            </div>
          </div>`;
  }
}

createModal.DEFAULTS = {
  title: '',
  uri: null,
  uri_options: {},
  label: 'Loading...',
  modal: {
      duration: 100
  }
};
