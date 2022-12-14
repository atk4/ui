module.exports = {
    extends: [
        'stylelint-config-standard',
    ],
    customSyntax: 'postcss-less',
    rules: {
        'at-rule-empty-line-before': null,
        'at-rule-name-case': null,
        'color-function-notation': 'legacy',
        indentation: 4,
        linebreaks: 'unix',
        'max-line-length': null,
        'no-descending-specificity': null,
        'rule-empty-line-before': null,
        'string-quotes': 'single',

        // TODO
        'at-rule-no-unknown': null, // 1 error: Unexpected unknown at-rule "@atkFooterHeight" (a stylelint bug?)
        'font-family-no-missing-generic-family-keyword': null, // 1 error that should be ignored with a comment
        'function-no-unknown': null, // 2 errors
        'no-duplicate-selectors': null, // 3 errors
        'selector-class-pattern': null, // 14 errors, example: Expected class selector ".atk-topMenu" to be kebab-case
    },
    reportNeedlessDisables: true,
};
