import atkPlugin from 'plugins/atkPlugin';
import debounce from 'debounce';
import $ from 'jquery';
import apiService from "../services/ApiService";

export default class scroll extends atkPlugin {

  main() {
    this.isWaiting = false;
    this.nextPage = this.settings.initialPage;
    //check if scroll apply vs Window or inside our element.
    this.isWindow = (this.$el.css('overflow-y') === 'visible');
    this.$scroll = this.isWindow ? $(window): this.$el;
    //is Inner the element itself or it's children.
    this.$inner = this.isWindow ? this.$el : this.$el.children();

    this.bindScrollEvent(this.$scroll);
  }

  bindScrollEvent($el) {
    $el.on('scroll', this.observe.bind(this));
  }

  /**
   * Check if scrolling require adding content.
   * @param e
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

    //console.log(Math.ceil(topHeight), Math.ceil(innerTop), totalHeight, Math.ceil(this.$scroll.height()));

    if (!this.isWaiting && totalHeight + this.settings.padding >= this.$inner.outerHeight()) {
      //console.log('scroll need');
      this.loadContent();
    }
  }

  setNextPage(page) {
    this.nextPage = page;
  }

  /**
   * Ask server for more content.
   */
  loadContent() {
    this.isWaiting = true;
    this.$inner.api({
      on: 'now',
      url: this.settings.uri,
      data: Object.assign({}, this.settings.uri_options, {page: this.nextPage}),
      method: 'GET',
      onComplete: this.onComplete.bind(this)
    });
  }

  onComplete(response, content) {
    if (response && response.html) {
      this.$inner.append(response.html);
    }
    if (response.success && response.message != 'done') {
      this.isWaiting = false;
      this.nextPage++;
    }
    //set response id to null for apiservice.
    response.id = null;
  }

}

scroll.DEFAULTS = {
  uri: null,
  uri_options: {},
  padding: 0,       //Minimum Bottom Space required prior to add content.
  initialPage: 2,
};