import $ from 'external/jquery';
import atk from 'atk';
import AtkPlugin from './atk.plugin';

export default class AtkJsSearchPlugin extends AtkPlugin {
    main() {
        this.urlArgs = {};
        this.state = { button: false, filter: false };
        this.textInput = this.$el.find('input[type="text"]');
        this.leftIcon = this.$el.find('.atk-filter-icon').hide();
        this.searchAction = this.$el.find('.atk-search-button');
        this.searchIcon = this.searchAction.find('i.atk-search-icon');
        this.removeIcon = this.searchAction.find('i.atk-remove-icon').hide();
        this.$el.data('previousValue', '');

        this.setInputAction();
        this.setSearchAction();
        this.onEscapeKeyAction();

        // set input initial value
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
        this.textInput.on('keyup', atk.createDebouncedFx((e) => {
            const options = $.extend({}, this.urlArgs, this.settings.urlOptions);
            if (e.target.value === '' || e.keyCode === 27) {
                this.doSearch(this.settings.url, null, options, () => {
                    this.setButtonState(false);
                    this.setFilterState(false);
                    this.textInput.val('');
                });
            } else if (e.target.value !== this.$el.data('previousValue')) {
                this.doSearch(this.settings.url, e.target.value, options, () => {
                    this.setButtonState(true);
                    this.setFilterState(true);
                });
            }
            this.$el.data('previousValue', e.target.value);
        }, this.settings.timeOut));
    }

    /**
     * Query server after pressing Enter.
     */
    onEnterAction() {
        this.textInput.on('keyup', (e) => {
            const options = $.extend({}, this.urlArgs, this.settings.urlOptions);
            if (e.keyCode === 13 && e.target.value) {
                this.doSearch(this.settings.url, e.target.value, options, () => {
                    this.setButtonState(true);
                    this.setFilterState(true);
                });
                this.$el.data('previousValue', e.target.value);
            } else if ((e.keyCode === 27 && e.target.value) || (e.keyCode === 13 && e.target.value === '')) {
                this.doSearch(this.settings.url, null, options, () => {
                    this.setButtonState(false);
                    this.setFilterState(false);
                });
                this.$el.data('previousValue', '');
                this.textInput.val('');
            } else if (this.$el.data('previousValue') !== e.target.value) {
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
            const options = $.extend({}, this.urlArgs, this.settings.urlOptions);
            if (this.state.button) {
                this.doSearch(this.settings.url, null, options, () => {
                    this.setButtonState(false);
                    this.setFilterState(false);
                });
                this.textInput.val('');
                this.$el.data('previousValue', '');
            }

            if (!this.state.button && this.textInput.val()) {
                this.doSearch(this.settings.url, this.textInput.val(), options, () => {
                    this.setButtonState(true);
                    this.setFilterState(true);
                });
            }
        });
    }

    /**
     * Allow to set filter initial input.
     * Mostly use on page load when input needs to be set to reflect a search state.
     *
     * @param {string} text The text input value.
     */
    setFilter(text) {
        this.textInput.val(text);
        this.setButtonState(true);
        this.setFilterState(true);
        this.$el.data('previousValue', text);
    }

    /**
     * More generic way to set URL argument.
     */
    setUrlArgs(arg, value) {
        this.urlArgs = Object.assign(this.urlArgs, { [arg]: value });
    }

    /**
     * Set Filter icon state.
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
     */
    doSearch(url, query, options, cb = function () {}) {
        const queryKey = this.settings.urlQueryKey;

        if (query) {
            options = $.extend(options, { [queryKey]: query });
        }

        if (this.settings.useAjax) {
            this.$el.api({
                on: 'now',
                url: url,
                data: options,
                method: 'GET',
                obj: this.$el,
                stateContext: this.searchAction,
                onComplete: cb,
            });
        } else {
            url = atk.urlHelper.removeParam(url, queryKey);
            if (options.__atk_reload) {
                delete options.__atk_reload;
            }
            url = atk.urlHelper.appendParams(url, options);
            window.location = url;
        }
    }
}

AtkJsSearchPlugin.DEFAULTS = {
    url: null,
    urlOptions: {},
    urlQueryKey: null,
    q: null,
    autoQuery: false,
    timeOut: 250,
    useAjax: true,
};
