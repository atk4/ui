module.exports = {
    env: {
        browser: true,
        es6: true,
    },
    extends: [
        'plugin:vue/essential',
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
        atk: true,
        $: true,
        jQuery: true,
    },
    rules: {
        indent: ['error', 4],
        'object-shorthand': ['error', 'never'],
        'func-names': ['error', 'never'],
        'no-param-reassign': 'off',
        'class-methods-use-this': 'off',
        'import/no-unresolved': 'off',
        'no-plusplus': 'off',
        'consistent-return': 'off',
        'no-nested-ternary': 'off',
        'import/prefer-default-export': 'off',
        'no-console': ['error', { allow: ['warn', 'error'] }],
        'no-underscore-dangle': ['error', { allow: ['__atkml', '__atkml_action', '__atk-reload'] }],
        'max-len': ['error', {
            code: 120,
            ignoreTemplateLiterals: true,
            ignoreComments: true,
            ignoreStrings: true,
        }],
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
    },
};
