import atkPlugin from 'plugins/atkPlugin';

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

<<<<<<< HEAD
      //add setting to our modal for modalService
      $m.data('modalSettings', {uri:options.uri, type:options.mode, arg:options.uri_options, needRemove:true, needCloseTrigger:true});

      //call semantic-ui modal
      $m.modal(options.modal).modal('show');
=======
    $m.modal($.extend({
      onHide: function (el) {
      return true;
    },
      onHidden: function () {
        $m.remove();
      },
      onVisible: function () {
        $.getJSON(options.uri, options.uri_options, function (resp) {
          $m.find('.atk-dialog-content').html(resp.html);
          const result = function(){ eval(resp.eval.replace(/<\/?script>/g, '')); }.call(this.obj);
          $m.modal('refresh');
        }).fail(function(){
          console.log('Error loading modal content.')
        });

        $m.on("close", '.atk-dialog-content', function () {
            $m.modal('hide');
        });
      }}, options.modal)).modal('show');
>>>>>>> atk4/develop
  }

  getDialogHtml(title) {
    return `<i class="close icon"></i>
          <div class="header">${title}</div>
          <div class="image content atk-dialog-content">
            <div class="ui active inverted dimmer">
              <div class="ui text loader">Loading</div>
            </div>
          </div>`;
  }
}

createModal.DEFAULTS = {
  title: '',
  uri: null,
  uri_options: {},
  modal: {
      duration: 100
  }
};
