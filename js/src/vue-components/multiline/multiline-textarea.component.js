export default {
    name: 'atk-textarea',
    template: '<textarea v-model="text" @input="handleChange"></textarea>',
    props: { value: [String, Number] },
    data: function () {
        return { text: this.value };
    },
    emits: ['input'],
    methods: {
        handleChange: function (event) {
            this.$emit('input', event.target.value);
        },
    },
};
