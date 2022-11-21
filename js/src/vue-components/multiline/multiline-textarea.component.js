export default {
    name: 'AtkMultilineTextarea',
    template: '<textarea v-model="modelValue" @input="onInput" />',
    props: ['modelValue'],
    emits: ['update:modelValue'],
    methods: {
        onInput: function (event) {
            this.$emit('update:modelValue', event.target.value);
        },
    },
};
