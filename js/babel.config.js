/**
 * Babel configuration file.
 * targets browser from Browserlist integration query.
 *
 * @type {*[][]}
 */
const presets = [
  [
    "@babel/env",
    {
      targets: "> 1% , not dead",
      "corejs": { version: '3.6', proposals: true },
      "useBuiltIns": "usage"
    },
  ],
];

module.exports = { presets };
