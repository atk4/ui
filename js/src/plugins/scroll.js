import atkPlugin from 'plugins/atkPlugin';
import $ from 'jquery';
import apiService from "../services/ApiService";

/**
 * Add dynamic scrolling to a View that can accept page argument in URL.
 *
 * default options are:
 *  padding: 20         The amount of padding needed prior to request a page load.
 *  initialPage: 1      The initial page load when calling this plugin.
 *  appendTo: null      The html element where new content should be append to.
 *  allowJsEval: false  Whether or not javascript send in server response should be evaluate.
 */

export default class scroll extends atkPlugin {

  main() {
    //check if we are initialized already because loading content
    //can recall this plugin and screw up page number.
    if (this.$el.data('__atkScroll')) {
      return false;
    }

    //set default option if not set.
    this.settings.options = Object.assign({padding: 20, initialPage: 1, appendTo: null, allowJsEval: false }, this.settings.options);

    this.isWaiting = false;
    this.nextPage = this.settings.options.initialPage + 1;
    //check if scroll apply vs Window or inside our element.
    this.isWindow = (this.$el.css('overflow-y') === 'visible');
    this.$scroll = this.isWindow ? $(window): this.$el;
    //is Inner the element itself or it's children.
    this.$inner = this.isWindow ? this.$el : this.$el.children();
    //the target element within container where new content is appendTo.
    this.$target = this.settings.options.appendTo ? this.$inner.find(this.settings.options.appendTo) : this.$inner;

    this.bindScrollEvent(this.$scroll);
  }

  /**
   * Bind scrolling event to an element.
   *
   * @param $el
   */
  bindScrollEvent($el) {
    $el.on('scroll', this.observe.bind(this));
  }

  /**
   * Check if scrolling require adding content.
   *
   * @param e //event
   */
  observe(e) {
    let borderTopWidth = parseInt(this.$el.css('borderTopWidth'), 10),
        borderTopWidthInt = isNaN(borderTopWidth) ? 0 : borderTopWidth,
        //this.$el padding top value.
        paddingTop = parseInt(this.$el.css('paddingTop'), 10) + borderTopWidthInt,
        //Either the scroll bar position using window or the element top position otherwise.
        topHeight = this.isWindow ? $(window).scrollTop() : this.$el.offset().top,
        //Inner top value. If using Window, this value does not change, otherwise represent the inner element top value when scroll.
        innerTop = this.$inner.length ? this.$inner.offset().top : 0,
        //The total height.
        totalHeight = Math.ceil(topHeight - innerTop + this.$scroll.height() + paddingTop);

    if (!this.isWaiting && totalHeight + this.settings.options.padding >= this.$inner.outerHeight()) {
      this.loadContent();
    }
  }

  /**
   * Set Next page to be loaded.
   *
   * @param page
   */
  setNextPage(page) {
    this.nextPage = page;
  }

  /**
   * Put scroll in idle mode.
   */
  idle(){
    this.isWaiting = true;
  }

  /**
   * Ask server for more content.
   */
  loadContent() {
    this.isWaiting = true;
    this.addLoader();
    this.$inner.api({
      on: 'now',
      url: this.settings.uri,
      data: Object.assign({}, this.settings.uri_options, {page: this.nextPage}),
      method: 'GET',
      onComplete: this.onComplete.bind(this),
    });
  }

  /**
   * Use response to append content to element and setup next content to be load.
   * Set response.id to null in order for apiService.onSuccess to bypass
   * replacing html content. Js return from server response will still be execute.
   *
   * @param response
   * @param element
   */
  onComplete(response, element) {
    this.removeLoader();
    if (response && response.success) {
      if (response.html && (response.message === "Success" || response.message === "Done")) {
        this.$target.append(response.html);
        if (response.message === "Success") {
            this.isWaiting = false;
            this.nextPage++;
        }
      }

      response.id = null;
      if (!this.settings.options.allowJsEval) {
        response.atkjs = null;
      }
    }
  }

  /**
   * Add loader.
   */
  addLoader(){
    let $parent = this.$inner.parent().hasClass('atk-overflow-auto') ? this.$inner.parent().parent() : this.$inner.parent();
    $parent.append($('<div id="atkScrollLoader"><div class="ui section hidden divider"></div><div class="ui active centered inline loader basic segment"></div></div>'));
  }

  /**
   * Remove loader.
   */
  removeLoader() {
    $('#atkScrollLoader').remove();
  }

}

scroll.DEFAULTS = {
  uri: null,
  uri_options: {},
  options: {}
};
