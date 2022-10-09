module.exports = {
    env: {
        browser: true,
        es6: true,
    },
    extends: [
        'plugin:vue/essential',
        'plugin:jsdoc/recommended',
        'airbnb-base',
    ],
    parserOptions: {
        ecmaVersion: '2020',
        sourceType: 'module',
    },
    plugins: [
        'vue',
    ],
    globals: {
        // TODO remove all global ignores here in favor of import, jQuery is loaded before main JS
        atk: true,
        $: true,
        jQuery: true,
    },
    rules: {
        indent: ['error', 4, { SwitchCase: 1 }],
        'object-shorthand': ['error', 'never'],
        'func-names': ['error', 'never'],
        'no-param-reassign': 'off',
        'class-methods-use-this': 'off',
        'import/no-unresolved': 'off',
        'no-plusplus': 'off',
        'consistent-return': 'off',
        'no-nested-ternary': 'off',
        'default-case': 'off',
        'import/prefer-default-export': 'off',
        'no-console': ['error', { allow: ['warn', 'error'] }],
        'no-underscore-dangle': ['error', { allow: ['__atk_reload', '__atkml', '__atkml_action'] }],
        'max-len': 'off',
        'prefer-template': ['off'],
        'no-unused-vars': ['error', { vars: 'all', args: 'none' }],
        'vue/no-unused-components': 'off',
        'spaced-comment': ['error', 'always', {
            line: {
                markers: ['/'],
                exceptions: ['-', '+'],
            },
            block: {
                markers: ['!'],
                exceptions: ['*'],
                balanced: true,
            },
        }],
        'jsdoc/require-param': 'off',
        'jsdoc/require-param-description': 'off',
        'jsdoc/require-returns': 'off',
        'jsdoc/require-returns-description': 'off',
        'jsdoc/require-jsdoc': 'off',
        'jsdoc/check-line-alignment': ['error', 'always'],
    },
    reportUnusedDisableDirectives: true,
};
