import atkPlugin from 'plugins/atkPlugin';
import $ from 'jquery';
import 'draggable';

/**
 * Make elements inside a container draggable and sortable.
 */
export default class jsSortable extends atkPlugin {

  main() {
    this.ids = [];
    this.injectStyles(this.settings.mirrorCss + this.settings.overCss);
    this.dragContainer = this.$el.find(this.settings.container);
    const sortable = new Draggable.Sortable(this.dragContainer[0], {draggable: this.settings.draggable, handle: '.'+this.settings.handleCssClass});

    sortable.on('drag:stop',  (e) => {
      const that = this;
      let ids = [];
      that.dragContainer.children().each(function(){
          if (!$(this).hasClass('draggable--original') && !$(this).hasClass('draggable-mirror')) {
            ids.push($(this).data(that.settings.dataLabel));
          }
       });
      that.ids = ids;
      that.sendSortOrders();
    });
  }

  getSortOrders() {
    this.sendSortOrders();
  }

  /**
   * Send orders to server via jsCallback.
   */
  sendSortOrders() {
    this.dragContainer.api({
      on: 'now',
      url: this.settings.uri,
      data: {order: this.ids.toString()},
      method: 'POST',
      obj: this.dragContainer,
    });
  }

  injectStyles(style) {
    $('head').append('<style type="text/css">' + style + '</style>');
  }
}

jsSortable.DEFAULTS = {
  uri: null,
  uri_options: {},
  container: 'tbody',
  draggable: 'tr',
  dataLabel: 'id',
  handleCssClass: 'atk-handle',
  mirrorCss: '.draggable-mirror {background: #fff!important;margin-left: 1%;opacity: 0.9;}',
  overCss: '.draggable--over { background: yellow !important; opacity: 0.5;}',
};
