module.exports = {
    extends: [
        'stylelint-config-standard',
    ],
    customSyntax: 'postcss-less',
    rules: {
        'at-rule-empty-line-before': null,
        'at-rule-name-case': null,
        'color-function-notation': 'legacy',
        'color-hex-case': 'lower',
        indentation: 4,
        linebreaks: 'unix',
        'max-line-length': null,
        'no-descending-specificity': null,
        'rule-empty-line-before': null,
        'string-quotes': 'double',

        // TODO
        'function-no-unknown': null, // 2 same errors: Unexpected unknown function "fade"
        'no-duplicate-selectors': null, // 3 errors
        'selector-class-pattern': null, // 14 errors, example: Expected class selector ".atk-topMenu" to be kebab-case
    },
    reportNeedlessDisables: true,
};
