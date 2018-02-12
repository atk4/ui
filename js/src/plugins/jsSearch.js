import atkPlugin from 'plugins/atkPlugin';

export default class jsSearch extends atkPlugin {

  main() {
    this.sortArgs = {};
    this.state = {button: false, filter: false};
    this.textInput = this.$el.find('input[type="text"]');
    this.leftIcon = this.$el.find('i.filter.icon').hide();
    this.searchAction = this.$el.find('.atk-action');
    this.searchContent = this.searchAction.html();
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
        that.setButtonState(true);
        that.setFilterState(true);
        that.$el.data('preValue', e.target.value);
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options, {'_q' : e.target.value}));
      } else if ((e.keyCode === 27 && e.target.value) || (e.keyCode === 13 && e.target.value === '')) {
        that.setButtonState(false);
        that.setFilterState(false);
        that.$el.data('preValue', '');
        that.textInput.val('');
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options));
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
        that.setButtonState(false);
        that.setFilterState(false);
        that.textInput.val('');
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options));
      }

      if (!that.state.button && that.textInput.val()) {
        that.setButtonState(true);
        that.setFilterState(true);
        that.doSearch(that.settings.uri,  $.extend({}, that.sortArgs, that.settings.uri_options, {'_q' : that.textInput.val()}));
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
      this.searchAction.html(this.getEraseContent());
    } else {
      this.searchAction.html(this.searchContent);
    }
    this.state.button = isOn;
  }

  /**
   * Send request to server using the search query.
   *
   * @param uri
   * @param options
   */
  doSearch(uri, options) {
    this.$el.api({
      on: 'now',
      url: uri,
      data: options,
      method: 'GET',
      obj: this.$el,
      stateContext: this.searchAction,
    });
  }

  /**
   * Return the html content for erase action button.
   *
   * @returns {string}
   */
  getEraseContent() {
    return `<i class="red remove icon" style=""></i>`;
  }
}

jsSearch.DEFAULTS = {
  uri: null,
  uri_options: {},
};
