import $ from 'jquery';
import atkPlugin from './atk.plugin';

export default class JsSearch extends atkPlugin {
    main() {
        this.urlArgs = {};
        this.state = { button: false, filter: false };
        this.textInput = this.$el.find('input[type="text"]');
        this.leftIcon = this.$el.find('.atk-filter-icon').hide();
        this.searchAction = this.$el.find('.atk-search-button');
        this.searchIcon = this.searchAction.find('i.atk-search-icon');
        this.removeIcon = this.searchAction.find('i.atk-remove-icon').hide();
        this.$el.data('preValue', '');

        this.setInputAction();
        this.setSearchAction();
        this.onEscapeKeyAction();

        // Set input initial value.
        if (this.settings.q) {
            this.setFilter(this.settings.q);
        }
    }

    /**
   * Set input field event handler.
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
        this.textInput.on('keyup', atk.debounce((e) => {
            const options = $.extend({}, this.urlArgs, this.settings.uri_options);
            if (e.target.value === '' || e.keyCode === 27) {
                this.doSearch(this.settings.uri, null, options, () => {
                    this.setButtonState(false);
                    this.setFilterState(false);
                    this.textInput.val('');
                });
            } else if (e.target.value !== this.$el.data('preValue')) {
                this.doSearch(this.settings.uri, e.target.value, options, () => {
                    this.setButtonState(true);
                    this.setFilterState(true);
                });
            }
            this.$el.data('preValue', e.target.value);
        }, this.settings.timeOut));
    }

    /**
   * Query server after pressing Enter.
   */
    onEnterAction() {
        this.textInput.on('keyup', (e) => {
            const options = $.extend({}, this.urlArgs, this.settings.uri_options);
            if (e.keyCode === 13 && e.target.value) {
                this.doSearch(this.settings.uri, e.target.value, options, () => {
                    this.setButtonState(true);
                    this.setFilterState(true);
                });
                this.$el.data('preValue', e.target.value);
            } else if ((e.keyCode === 27 && e.target.value) || (e.keyCode === 13 && e.target.value === '')) {
                this.doSearch(this.settings.uri, null, options, () => {
                    this.setButtonState(false);
                    this.setFilterState(false);
                });
                this.$el.data('preValue', '');
                this.textInput.val('');
            } else if (this.$el.data('preValue') !== e.target.value) {
                this.setButtonState(false);
            }
        });
    }

    /**
   * When Search has the focus and the Escape key is pressed, clear Search text.
   * When Search text is already empty the event will bubble up normally.
   */
    onEscapeKeyAction() {
        this.textInput.keydown((e) => {
            if (this.textInput.val() !== '' && e.key === 'Escape') {
                this.setButtonState(false);
                this.setFilterState(false);
                this.textInput.val('');
                return false;
            }
        });
    }

    /**
   * Set Search button event handler.
   */
    setSearchAction() {
        this.searchAction.on('click', (e) => {
            const options = $.extend({}, this.urlArgs, this.settings.uri_options);
            if (this.state.button) {
                this.doSearch(this.settings.uri, null, options, () => {
                    this.setButtonState(false);
                    this.setFilterState(false);
                });
                this.textInput.val('');
                this.$el.data('preValue', '');
            }

            if (!this.state.button && this.textInput.val()) {
                this.doSearch(this.settings.uri, this.textInput.val(), options, () => {
                    this.setButtonState(true);
                    this.setFilterState(true);
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
    setFilter(text) {
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
        this.urlArgs = Object.assign(this.urlArgs, { [arg]: value });
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
    doSearch(uri, query, options, cb = function () {}) {
        if (query) {
            options = $.extend(options, { _q: query });
        }

        if (this.settings.useAjax) {
            this.$el.api({
                on: 'now',
                url: uri,
                data: options,
                method: 'GET',
                obj: this.$el,
                stateContext: this.searchAction,
                onComplete: cb,
            });
        } else {
            uri = $.atkRemoveParam(uri, '_q');
            if (options.__atk_reload) {
                delete options.__atk_reload;
            }
            uri = $.atkAddParams(uri, options);
            window.location = uri;
        }
    }
}

JsSearch.DEFAULTS = {
    uri: null,
    uri_options: {},
    q: null,
    autoQuery: false,
    timeOut: 300,
    useAjax: true,
};
