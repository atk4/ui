import atkPlugin from 'plugins/atkPlugin';

export default class createModal extends atkPlugin {

  main() {
      const options = this.settings;
      let $m = $('<div class="atk-modal ui modal scrolling"/>').appendTo('body').html(this.getDialogHtml(options.title));

      $m.modal($.extend({
          onHide: function (el) {
              return true;
          },
          onHidden: function () {
              $m.remove();
          },
          onVisible: function () {
              let $content = $m.find('.atk-dialog-content');
              if (options.mode === 'json') {
                  $.getJSON(options.uri, options.uri_options, function (resp) {
                      $content.html(resp.html);
                      const result = function(){ eval(resp.eval.replace(/<\/?script>/g, '')); }.call(this.obj);
                  }).fail(function(){
                      console.log('Error loading modal content.')
                  });
              } else {
                  $content
                      .load($.addParams(options.uri, options.uri_options), function() {
                          $m.modal("refresh");
                      });
              }
              //Attach closing handler
              $m.on("close", '.atk-dialog-content', function () {
                  $m.modal('hide');
              });
          }}, options.modal)).modal('show');
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
