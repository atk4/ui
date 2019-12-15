import atkPlugin from './atk.plugin';
import debounce from 'debounce';
import $ from 'jquery';

export default class jsSearch extends atkPlugin {

  main() {
    this.urlArgs = {};
    this.state = {button: false, filter: false};
    this.textInput = this.$el.find('input[type="text"]');
    this.leftIcon = this.$el.find('.atk-filter-icon').hide();
    this.searchAction = this.$el.find('.atk-search-button');
    this.searchIcon = this.searchAction.find('i.atk-search-icon');
    this.removeIcon  = this.searchAction.find('i.atk-remove-icon').hide();
    this.$el.data('preValue', '');

    this.setInputAction();
    this.setSearchAction();
    this.onEscapeKeyAction();

    //Set input initial value.
    if (this.settings.q) {
      this.setFilter(this.settings.q);
    }
  }

  /**
   * Set input field event handler.
   *
   * @param that
   */
  setInputAction() {
    if (this.settings.autoQuery) {
      this.onAutoQueryAction();
    } else {
      this.onEnterAction();
    }
  }

  /**
   * Query server on each keystroke after proper timeout.
   */
  onAutoQueryAction() {
    const that = this;
    this.textInput.on('keyup', debounce(function(e){
      if (e.target.value === '' || e.keyCode === 27) {
        that.doSearch(that.settings.uri, null, $.extend({}, that.urlArgs, that.settings.uri_options), function(){
          that.setButtonState(false);
          that.setFilterState(false);
          that.textInput.val('');
        });
      } else if (e.target.value != that.$el.data('preValue')){
        that.doSearch(that.settings.uri, e.target.value, $.extend({}, that.urlArgs, that.settings.uri_options/*, {'_q' : e.target.value}*/), function(){
          that.setButtonState(true);
          that.setFilterState(true);
        });
      }
      that.$el.data('preValue', e.target.value);
    }, this.settings.timeOut));
  }

  /**
   * Query server after pressing Enter.
   */
  onEnterAction() {
    const that = this;
    this.textInput.on('keyup', function(e) {
      if (e.keyCode === 13 && e.target.value) {
        that.doSearch(that.settings.uri, e.target.value, $.extend({}, that.urlArgs, that.settings.uri_options), function(){
          that.setButtonState(true);
          that.setFilterState(true);
        });
        that.$el.data('preValue', e.target.value);
      } else if ((e.keyCode === 27 && e.target.value) || (e.keyCode === 13 && e.target.value === '')) {
        that.doSearch(that.settings.uri, null, $.extend({}, that.urlArgs, that.settings.uri_options), function(){
          that.setButtonState(false);
          that.setFilterState(false);
        });
        that.$el.data('preValue', '');
        that.textInput.val('');
      } else if (that.$el.data('preValue') !== e.target.value) {
        that.setButtonState(false);
      }
    });
  }

  /**
   * When Search has the focus and the Escape key is pressed, clear Search text.
   * When Search text is already empty the event will bubble up normally.
   */
  onEscapeKeyAction() {
    const that = this;
    this.textInput.keydown(function(e) {
      if (that.textInput.val() != '' && e.key === 'Escape') {
        that.setButtonState(false);
        that.setFilterState(false);
        that.textInput.val('');
        return false;
      }
    });
  }

  /**
   * Set Search button event handler.
   *
   * @param that
   */
  setSearchAction() {
    const that = this;
    this.searchAction.on('click', function(e){
      if (that.state.button){
        that.doSearch(that.settings.uri, null, $.extend({}, that.urlArgs, that.settings.uri_options), function() {
          that.setButtonState(false);
          that.setFilterState(false);
        });
        that.textInput.val('');
        that.$el.data('preValue', '');
      }

      if (!that.state.button && that.textInput.val()) {
        that.doSearch(that.settings.uri,  that.textInput.val(), $.extend({}, that.urlArgs, that.settings.uri_options), function() {
          that.setButtonState(true);
          that.setFilterState(true);
        });
      }
    });
  }

  /**
   * Add argument to url for sorting purpose.
   *
   * @Deprecated Use setUrlArgs instead.
   *
   * @param name
   * @param sortBy
   */
  setSortArgs(name, sortBy) {
    this.setUrlArgs(name, sortBy);
  }

  /**
   * Allow to set filter initial input.
   * Mostly use on page load
   * when input need to be set to reflect a search state.
   *
   * @param text || The text input value.
   */
  setFilter(text){
    this.textInput.val(text);
    this.setButtonState(true);
    this.setFilterState(true);
    this.$el.data('preValue', text);
  }

  /**
   * More generic way to set url argument.
   *
   * @param arg
   * @param value
   */
  setUrlArgs(arg, value) {
    this.urlArgs = Object.assign(this.urlArgs,{[arg] : value});
    return;
  }

  /**
   * Set Filter icon state.
   *
   * @param isOn
   */
  setFilterState(isOn) {
    if (isOn) {
      this.leftIcon.show();
    } else {
      this.leftIcon.hide();
    }
    this.state.filter = isOn;
  }

  /**
   * Set search button state.
   *
   * @param isOn
   */
  setButtonState(isOn) {
    if (isOn) {
      this.searchIcon.hide();
      this.removeIcon.show();
    } else {
      this.searchIcon.show();
      this.removeIcon.hide();
    }
    this.state.button = isOn;
  }

  /**
   * Send request to server using the search query.
   *
   * @param uri
   * @param options
   */
  doSearch(uri, query, options, cb = function(){}) {
    if (query) {
      options = $.extend(options, {'_q' : query});
    }
    //if we are not using ajax simply reload page.
    if (!this.settings.useAjax) {
      uri = $.atkRemoveParam(uri, '_q');
      delete options.__atk_reload;

      // if (query) {
      //   uri = $.atkAddParams(uri, {'_q': query});
      // }
      uri = $.atkAddParams(uri, options);
      window.location = uri;
      return;
    }
    this.$el.api({
      on: 'now',
      url: uri,
      data: options,
      method: 'GET',
      obj: this.$el,
      stateContext: this.searchAction,
      onComplete: cb,
    });
  }
}

jsSearch.DEFAULTS = {
  uri: null,
  uri_options: {},
  q: null,
  autoQuery: false,
  timeOut: 500,
  useAjax: true
};
