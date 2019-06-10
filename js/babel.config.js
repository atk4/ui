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
      "useBuiltIns": "usage"
    },
  ],
];

module.exports = { presets };