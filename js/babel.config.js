/**
 * Babel configuration file.
 * targets browser from Browserlist integration query.
 *
 * @type {Array.<Array.<*>>}
 */
const presets = [
    [
        '@babel/env',
        {
            targets: '> 1%, not dead',
            corejs: { version: '3.6', proposals: true },
            useBuiltIns: 'usage',
        },
    ],
    {
        plugins: ['@babel/plugin-transform-runtime'],
    },
];

module.exports = { presets: presets };
