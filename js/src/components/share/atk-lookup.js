/**
 * Wrapper for Semantic Ui dropdown component into a lookup component.
 *
 * Props
 *  config :
 *      url : the callback url. Callback should return model data in form
 *            of {key: model_id, text: model_title, value: model_id}
 *      reference: the reference field name associate with model or hasOne name.
 *                This field name will be sent along with url callback parameter as of 'field=name'.
 *      ui: the css class name to apply to dropdown.
 *      Note: The remaining config object may contain any or sui-dropdown {props: value} pair.
 *
 *  value: The selected value.
 *  optionalValue: The initial list of options for the dropdown.
 */

const template = `<sui-dropdown 
                    ref="drop"
                    v-bind="dropdownProps" 
                    :loading="isLoading" 
                    @input="onChange" 
                    @filtered="onFiltered" 
                    v-model="current" 
                    :class="css"></sui-dropdown>`;

export default {
    name: 'atk-lookup',
    template: template,
    props: ['config', 'value', 'optionalValue'],
    data: function () {
        const {
            url, reference, ui, ...suiDropdown
        } = this.config;
        suiDropdown.selection = true;

        return {
            dropdownProps: suiDropdown,
            current: this.value,
            url: url || null,
            css: [ui],
            isLoading: false,
            field: reference,
            query: '',
            temp: '',
        };
    },
    mounted: function () {
        if (this.optionalValue) {
            this.dropdownProps.options = Array.isArray(this.optionalValue) ? this.optionalValue : [this.optionalValue];
        }
    },
    methods: {
        onChange: function (value) {
            this.$emit('onChange', value);
        },
        /**
     * Receive user input text for search.
     */
        onFiltered: function (inputValue) {
            if (inputValue) {
                this.isLoading = true;
            }
            this.temp = inputValue;
            atk.debounce(() => {
                if (this.query !== this.temp) {
                    this.query = this.temp;
                    if (this.query) {
                        this.fetchItems(this.query);
                    }
                }
            }, 300).call(this);
        },
        /**
     * Fetch new data from server.
     */
        fetchItems: async function (q) {
            try {
                const data = { atk_vlookup_q: q, atk_vlookup_field: this.field };
                const response = await atk.apiService.suiFetch(this.url, { method: 'get', data: data });
                if (response.success) {
                    this.dropdownProps.options = response.results;
                }
                this.isLoading = false;
            } catch (e) {
                console.error(e);
                this.isLoading = false;
            }
        },
    },
};
