/* global module */
module.exports = {
    env: {
        browser: true,
        es6: true,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/essential',
    ],
    parserOptions: {
        sourceType: 'module',
    },
    plugins: [
        'vue',
    ],
    rules: {
        indent: ['error', 4],
        'object-shorthand': ['error', 'never'],
        'prefer-template': ['off'],
        'no-unused-vars': ['error', { 'vars': 'all', 'args': 'none' }],
        'vue/no-unused-components': 'off',
    },
};
