export default {
    name: 'atk-multiline-textarea',
    template: '<textarea v-model="modelValue" @input="onInput"></textarea>',
    props: ['modelValue'],
    emits: ['update:modelValue'],
    methods: {
        onInput: function (event) {
            this.$emit('update:modelValue', event.target.value);
        },
    },
};
