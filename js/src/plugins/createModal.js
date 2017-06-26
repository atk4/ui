import $ from 'jQuery';
$.fn.modal = require('semantic-ui-modal');

export default class createModal {
  constructor(element, options) {
    const container = this.getDialogHtml(options.title);
    const m = $('<div>').appendTo('body').addClass('ui scrolling modal').html(container);

    m.modal({
      onHide: function () {
        m.children().remove();
        return true;
      },
      onShow: function () {
        const $el = $(this);
        $.getJSON(options.uri, options.uri_options, function (resp) {
          console.log($);
          $el.find('.atk-dialog-content').html(resp.html);
          let result = function(){eval(resp.eval.replace(/<\/?script>/g, '')); }.call(this.obj);
          //global.eval(resp.eval.replace(/<\/?script>/g, ''));
        });
      }})
      .modal('show');

    m.find('.atk-dialog-content').data('opener', this)
    .on('close', function () {
      m.modal('hide');
      m.remove();
    });
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
  uri_options: {}
};
