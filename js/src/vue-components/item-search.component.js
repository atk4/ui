import $ from 'external/jquery';
import atk from 'atk';

/**
 * Will allow user to send data query request to server.
 * Request should filter the data and reload the data view.
 * The request is send using Fomantic-UI api.
 *
 * Properties need for this component are:
 *
 * context: string, a jQuery selector where the 'loading' class will be apply by Fomantic-UI - default to this component.
 * url:     string, the URL to call.
 * q:       string, the initial string for the query. Useful if this search is part of the relaod.
 * reload:  string, an Id selector for jQuery, '#' is append automatically.
 */

const template = `<div class="atk-item-search" :class="inputCss">
      <input class="ui"
        v-model="query"
        type="text" placeholder="Search..."
        @keyup="onKeyup"
        @keyup.esc="onEscape"
        name="atk-vue-search" />
        <i class="atk-search-icon" :class="classIcon"></i><span style="width: 12px; cursor: pointer;" @click="onClear"></span>
    </div>
`;

export default {
    name: 'atk-item-search',
    template: template,
    props: {
        context: String,
        url: String,
        q: String,
        reload: String,
        queryArg: String,
        options: Object,
    },
    data: function () {
        return {
            query: this.q,
            temp: this.q,
            isActive: false,
            extraQuery: null,
            inputCss: this.options.inputCss,
        };
    },
    computed: {
        classIcon: function () {
            return {
                'search icon': (this.query === null || this.query === ''),
                'remove icon': this.query !== null,
            };
        },
    },
    methods: {
        onKeyup: function () {
            if (!this.onKeyup.debouncedFx) {
                this.onKeyup.debouncedFx = atk.createDebouncedFx((e) => {
                    this.onKeyup.debouncedFx = null;
                    if (this.query !== this.temp) {
                        if (this.query === '') {
                            this.query = null;
                        }
                        this.sendQuery();
                        this.temp = this.query;
                    }
                }, this.options.inputTimeOut);
            }
            this.onKeyup.debouncedFx.call(this);
        },
        onEscape: function () {
            if (this.query !== null) {
                this.query = null;
                this.temp = null;
                this.sendQuery();
            }
        },
        onEnter: function () {
            if (this.query !== null) {
                this.query = null;
                this.temp = null;
                this.sendQuery();
            }
        },
        onClear: function () {
            if (this.query) {
                this.query = null;
                this.temp = null;
                this.sendQuery();
            }
        },
        sendQuery: function () {
            const that = this;
            const options = $.extend({}, this.extraQuery, { __atk_reload: this.reload, [this.queryArg]: this.query });
            const $reload = $('#' + this.reload);
            this.isActive = true;
            $reload.api({
                on: 'now',
                url: this.url,
                data: options,
                method: 'GET',
                stateContext: (this.context) ? $(this.context) : $(this.$el),
                onComplete: function (e, r) {
                    that.isActive = false;
                },
            });
        },
    },
};
