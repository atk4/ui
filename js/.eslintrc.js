module.exports = {
    env: {
        browser: true,
        es2020: true,
    },
    extends: [
        'plugin:vue/essential',
        'airbnb-base',
    ],
    parserOptions: {
        ecmaVersion: 11,
        sourceType: 'module',
    },
    plugins: [
        'vue',
    ],
    rules: {
        indent: ['error', 4],
        'object-shorthand': ['error', 'never'],
        'prefer-template': ['off'],
    },
};
