import atkPlugin from 'plugins/atkPlugin';

export default class jsSearch extends atkPlugin {

  main() {
    this.sortArgs = {};
    this.state = {button: false, filter: false};
    this.textInput = this.$el.find('input[type="text"]');
    this.leftIcon = this.$el.find('.atk-filter-icon').hide();
    this.searchAction = this.$el.find('.atk-search-button');
    this.searchIcon = this.searchAction.find('i.atk-search-icon');
    this.removeIcon  = this.searchAction.find('i.atk-remove-icon').hide();
    this.$el.data('preValue', '');

    this.setInputAction(this);
    this.setSearchAction(this);
  }

  /**
   * Set input field event handler.
   *
   * @param that
   */
  setInputAction(that) {
    this.textInput.on('keyup', function(e) {
      if (e.keyCode === 13 && e.target.value) {
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options, {'_q' : e.target.value}), function(){
          that.setButtonState(true);
          that.setFilterState(true);
        });
        that.$el.data('preValue', e.target.value);
      } else if ((e.keyCode === 27 && e.target.value) || (e.keyCode === 13 && e.target.value === '')) {
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options), function(){
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
   * Set Search button event handler.
   *
   * @param that
   */
  setSearchAction(that) {
    this.searchAction.on('click', function(e){
      if (that.state.button){
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options), function() {
          that.setButtonState(false);
          that.setFilterState(false);
        });
        that.textInput.val('');
      }

      if (!that.state.button && that.textInput.val()) {
        that.doSearch(that.settings.uri,  $.extend({}, that.sortArgs, that.settings.uri_options, {'_q' : that.textInput.val()}), function() {
          that.setButtonState(true);
          that.setFilterState(true);
        });
      }
    });
  }

  /**
   * Add argument to url for sorting purpose.
   *
   * @param name
   * @param sortBy
   */
  setSortArgs(name, sortBy) {
    this.sortArgs[name] = sortBy;
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
  doSearch(uri, options, cb = function(){}) {
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
};
