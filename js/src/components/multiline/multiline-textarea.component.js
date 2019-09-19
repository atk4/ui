/**
 * Simple text area input to display in multiline component.
 */
export default {
  name: 'atk-textarea',
  template: `<textarea v-model="text" @input="handleChange"></textarea>`,
  props: {value: [String, Number]},
  data() {
    return {text: this.value};
  },
  methods: {
    handleChange: function(event) {
      this.$emit('input', event.target.value);
    },
  },
}
