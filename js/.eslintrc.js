module.exports = {
    env: {
        browser: true,
        es6: true,
    },
    extends: [
        'plugin:jsdoc/recommended',
        'airbnb-base',
        'plugin:unicorn/recommended',
        'plugin:vue/vue3-recommended',
    ],
    parserOptions: {
        ecmaVersion: '2020',
        sourceType: 'module',
    },
    settings: {
        'import/resolver': 'webpack',
    },
    rules: {
        indent: ['error', 4, { SwitchCase: 1 }],
        curly: ['error', 'all'],
        'object-shorthand': ['error', 'never'],
        'func-names': ['error', 'never'],
        'no-param-reassign': 'off',
        'class-methods-use-this': 'off',
        'no-plusplus': 'off',
        'consistent-return': 'off',
        'no-nested-ternary': 'off',
        'default-case': 'off',
        'no-console': ['error', { allow: ['warn', 'error'] }],
        'no-restricted-syntax': 'off',
        'no-underscore-dangle': 'off',
        'max-len': 'off',
        'prefer-template': 'off',
        'no-unused-vars': ['error', { vars: 'all', args: 'none' }],
        'padding-line-between-statements': ['error', {
            blankLine: 'always',
            prev: '*',
            next: ['continue', 'break', 'export', 'return', 'throw'],
        }],
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
        'comma-dangle': ['error', {
            arrays: 'always-multiline',
            objects: 'always-multiline',
            functions: 'never',
            imports: 'always-multiline',
            exports: 'always-multiline',
        }],
        'import/no-unresolved': 'off',
        'import/prefer-default-export': 'off',
        'import/extensions': ['error', 'always', {
            '': 'never',
            js: 'never',
            vue: 'never',
        }],
        'vue/html-indent': ['error', 4],
        'vue/attribute-hyphenation': ['error', 'never'],
        'jsdoc/require-param': 'off',
        'jsdoc/require-param-description': 'off',
        'jsdoc/require-returns': 'off',
        'jsdoc/require-returns-description': 'off',
        'jsdoc/require-jsdoc': 'off',
        'jsdoc/check-line-alignment': ['error', 'always'],
        'unicorn/catch-error-name': 'off',
        'unicorn/no-array-callback-reference': 'off',
        'unicorn/no-lonely-if': 'off',
        'unicorn/no-negated-condition': 'off',
        'unicorn/no-null': 'off',
        'unicorn/no-this-assignment': 'off',
        'unicorn/prefer-module': 'off',
        'unicorn/prevent-abbreviations': 'off',
        'unicorn/switch-case-braces': ['error', 'avoid'],
    },
    reportUnusedDisableDirectives: true,
};
