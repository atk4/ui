/**
 * Wrapper for Semantic Ui dropdown component into a lookup component.
 *
 * Props
 */

const template = `<sui-dropdown 
                    v-bind="dropdownProps" 
                    :loading="isLoading" 
                    @input="onChange" 
                    @filtered="onFiltered" 
                    v-model="current" 
                    class="class"></sui-dropdown>` ;

export default {
  name: 'atk-lookup',
  template: template,
  props: ['config', 'value'],
  data: function () {

    const {url, reference, ...suiDropdown} = this.config;

    suiDropdown.selection = true;

    return {
      dropdownProps: suiDropdown,
      current: this.value,
      url: url || null,
      class: 'ui mini basic button',
      isLoading: false,
    };
  },
  mounted: function () {

  },
  computed: {
    // isLoading: function () {
    //   return this.loading;
    // }
  },
  methods: {
    getValue: function() {
      return this.value;
    },
    setNewItem: function(id) {
      console.log(id);
    },
    onChange: function(e) {
      console.log('change', e);
    },
    onFiltered: function(e) {
      this.isLoading = true;
      atk.debounce((e) => {
        this.fetchItems(e);
      }, 3000).call(this);

      console.log('filter', e);

    },
    fetchItems: async function (query) {
      try {
        const response = await atk.apiService.suiFetch(this.url, { method: 'get' });
        console.log('resp', response);
        this.isLoading = false;
      } catch (e) {
        console.error(e);
      }
    }
  },
};