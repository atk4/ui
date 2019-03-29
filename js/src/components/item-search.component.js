import debounce from 'debounce';
import $ from "jquery";

/**
 * Vue component
 * Will allow user to send data queyr request to server.
 * Request should filter the data and reload the data view.
 * The request is send using semantic-ui api.
 *
 * Template is done inline: item-search.html
 *
 * Properties need for this component are:
 *
 * context: string, a jQuery selector where the 'loading' class will be apply by semantic-ui;
 *          - default to this component.
 * url:     string, the url to call;
 * q:       string, the initial string for the query. Useful if this search is part of the relaod.
 * reload:  string, an Id selector for jQuery, '#' is append automatically.
 *
 */
export default {
  name: 'atk-item-search',
  props: {
    context: String,
    url: String,
    q: String,
    reload: String,
  },
  data: function () {
    return { query: this.q, temp: this.q, isActive: false, extraQuery: null};
  },
  computed: {
    classIcon: function() {
      return {
        'search icon': (this.query === null || this.query === ''),
        'remove icon': this.query !== null
      }
    }
  },
  methods: {
    onChange: debounce(function(e){
      if (this.query != this.temp) {
        if (this.query === '') this.query = null;
        this.sendQuery();
        this.temp = this.query;
      }
    }, 300),
    onEscape: function() {
      if (this.query !== null) {
        this.query = null;
        this.temp = null;
        this.sendQuery();
      }
    },
    onEnter: function() {
      if (this.query !== null) {
        this.query = null;
        this.temp = null;
        this.sendQuery();
      }
    },
    onClear: function() {
      if (this.query) {
        this.query = null;
        this.temp = null;
        this.sendQuery();
      }
    },
    sendQuery: function() {
      const that = this;
      const options = $.extend({}, this.extraQuery, {__atk_reload: this.reload, '_q' : this.query});
      const $reload = $('#'+this.reload);
      this.isActive = true;
      $reload.api({
        on: 'now',
        url: this.url,
        data: options,
        method: 'GET',
        stateContext: (this.context) ? $(this.context) : $(this.$el),
        onComplete: function(e,r) {
          that.isActive = false;
        }
      });
    }
  }
}
