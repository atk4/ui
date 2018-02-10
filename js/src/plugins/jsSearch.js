import atkPlugin from 'plugins/atkPlugin';

export default class jsSearch extends atkPlugin {

  main() {
    this.sortArgs = {};
    this.filterState = false;
    this.textInput = this.$el.find('input[type="text"]');
    this.searchAction = this.$el.find('.atk-action');
    this.searchContent = this.searchAction.html();

    this.setAction();
  }

  /**
   * Set text input and button event handler.
   */
  setAction() {
    const that = this;
    this.textInput.on('keydown', function(e) {
      if (e.keyCode === 13 && e.target.value) {
        that.setFilterState(true);
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options, {'_q' : e.target.value}));
      }
      if ((e.keyCode === 27 && e.target.value) || (e.keyCode === 13 && e.target.value === '')) {
        that.setFilterState(false);
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options));
      }
    });

    this.searchAction.on('click', function(e){
      if (that.filterState){
        that.setFilterState(false);
        that.doSearch(that.settings.uri, $.extend({}, that.sortArgs, that.settings.uri_options));
      }

      if (!that.filterState && that.textInput.val()) {
        that.setFilterState(true);
        that.doSearch(that.settings.uri,  $.extend({}, that.sortArgs, that.settings.uri_options, {'_q' : e.target.value}));
      }
    });
  }

  setSortArgs(name, sortBy) {
    this.sortArgs[name] = sortBy;
  }

  setFilterState(isFilterOn) {
    if (isFilterOn) {
      this.searchAction.html(this.getEraseContent());
    } else {
      this.searchAction.html(this.searchContent);
      this.textInput.val('');
    }
    this.filterState = isFilterOn;
  }

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
