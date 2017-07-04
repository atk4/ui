
export default class createModal {
  constructor(element, options) {

    let $m = $('<div class="atk-modal ui modal scrolling"/>').appendTo('body').html(this.getDialogHtml(options.title));

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
        }).fail(function(){
          console.log('Error loading modal content.')
        });
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
