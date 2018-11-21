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

    let defaultSettings = {
      padding: 20,
      initialPage: 1,
      appendTo: null,
      allowJsEval: false ,
      hasFixTableHeader: false,
      tableContainerHeight: 400,
      tableHeaderColor: '#ffffff'
    };
    //set default option if not set.
    this.settings.options = Object.assign({}, defaultSettings, this.settings.options);

    this.isWaiting = false;
    this.nextPage = this.settings.options.initialPage + 1;

    if (this.settings.options.hasFixTableHeader) {
      this.isWindow = false;
      this.$scroll = this.$el.parent();
      this.$inner = this.$el;
      this.setTableHeader();
    } else {
      //check if scroll apply vs Window or inside our element.
      this.isWindow = (this.$el.css('overflow-y') === 'visible');
      this.$scroll = this.isWindow ? $(window): this.$el;
      //is Inner the element itself or it's children.
      this.$inner = this.isWindow ? this.$el : this.$el.children();
    }

    //the target element within container where new content is appendTo.
    this.$target = this.settings.options.appendTo ? this.$inner.find(this.settings.options.appendTo) : this.$inner;

    this.bindScrollEvent(this.$scroll);


    // if there is no scrollbar, then try to load next page too
    if (!this.hasScrollbar()) {
      this.loadContent();
    }
  }

  /**
   * Add fix table header.
   */
  setTableHeader() {
    if (this.$el.parent().length > 0) {
      let $tableCopy;
      this.$el.parent().height(this.settings.options.tableContainerHeight);
      this.$el.addClass('fixed');
      $tableCopy = this.$el.clone(true, true);
      $tableCopy.attr('id', $tableCopy.attr('id')+'_');
      $tableCopy.find('tbody, tfoot').remove();
      $tableCopy.css({
        'position':'absolute',
        'background-color' : this.settings.options.tableHeaderColor,
        'border' : this.$el.find('th').eq(1).css('border-left')
      });
      this.$scroll.prepend($tableCopy);
      this.$el.find('thead').hide();
      this.$el.css('margin-top', $tableCopy.find('thead').height());
    }
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
        //Either the scroll bar position using window or the container element top position otherwise.
        topHeight = this.isWindow ? $(window).scrollTop() : this.$scroll.offset().top,
        //Inner top value. If using Window, this value does not change, otherwise represent the inner element top value when scroll.
        innerTop = this.$inner.length ? this.$inner.offset().top : 0,
        //The total height.
        totalHeight = Math.ceil(topHeight - innerTop + this.$scroll.height() + paddingTop);


    if (!this.isWaiting && totalHeight + this.settings.options.padding >= this.$inner.outerHeight()) {
      this.loadContent();
    }
  }

  /**
   * Check if container element has vertical scrollbar.
   *
   * @return bool
   */
  hasScrollbar() {
    let innerHeight = this.isWindow ? Math.ceil(this.$el.height()) : Math.ceil(this.$inner.height());
    let scrollHeight = Math.ceil(this.$scroll.height());
    return innerHeight > scrollHeight;
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
      stateContext: this.settings.options.hasFixTableHeader ? this.$el : this.$inner,
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
      if (response.html) {
        // Done - no more pages
        if (response.message === "Done") {
          this.$target.append(response.html);
          this.idle()
        }
        // Success - will have more pages
        if (response.message === "Success") {
          this.$target.append(response.html);
          this.isWaiting = false;
          this.nextPage++;
          // if there is no scrollbar, then try to load next page too
          if (!this.hasScrollbar()) {
            this.loadContent();
          }
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
