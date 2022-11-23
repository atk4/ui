import atk from 'atk';

/**
 * Wrapper for Fomantic-UI dropdown component into a lookup component.
 *
 * Properties:
 * config:
 * url: the callback URL. Callback should return model data in form of { key: modelId, text: modelTitle, value: modelId }
 * reference: the reference field name associate with model or hasOne name. This field name will be sent along with URL callback parameter as of 'field=name'.
 * ui: the css class name to apply to dropdown.
 * Note: The remaining config object may contain any or SuiDropdown { props: value } pair.
 *
 * value: The selected value.
 * optionalValue: The initial list of options for the dropdown.
 */
export default {
    name: 'AtkLookup',
    template: `
        <SuiDropdown
            v-bind="dropdownProps"
            ref="drop"
            ` /* :loading="isLoading" */
            + `@update:modelValue="onChange"
            @filtered="onFiltered"
            v-model="current"
            :class="css"
        ></SuiDropdown>`,
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
    emits: ['update:modelValue'],
    methods: {
        onChange: function (value) {
            this.current = value.value;
            this.$emit('update:modelValue', this.current);
        },
        /**
         * Receive user input text for search.
         */
        onFiltered: function (inputValue) {
            if (inputValue) {
                this.isLoading = true;
            }

            if (!this.onFiltered.debouncedFx) {
                this.onFiltered.debouncedFx = atk.createDebouncedFx(() => {
                    this.onFiltered.debouncedFx = null;
                    if (this.query !== this.temp) {
                        this.query = this.temp;
                        if (this.query) {
                            this.fetchItems(this.query);
                        }
                    }
                }, 250);
            }
            this.temp = inputValue;
            this.onFiltered.debouncedFx(this);
        },
        /**
         * Fetch new data from server.
         */
        fetchItems: async function (q) {
            try {
                const data = { atkVueLookupQuery: q, atkVueLookupField: this.field };
                const response = await atk.apiService.suiFetch(this.url, { method: 'get', data: data });
                if (response.success) {
                    this.dropdownProps.options = response.results;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isLoading = false;
            }
        },
    },
};
