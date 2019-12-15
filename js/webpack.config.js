/*global __dirname:true, require:true*/
/**
 * Webpack v4 configuration file.
 *
 * Use mode from env variable pass to webpack in order to
 * differentiate build mode.
 * Use a function that return configuration object based
 * on env variable.
 *
 * Using Development
 *  - set webpack config mode to development
 *  - devtools will use source-map under atk name;
 *
 * Using Production
 *  - set webpack config mode to production
 *  - change name of output file by adding .min
 *
 * Module export will output default value
 * using libraryExport : 'default' for backward
 * compatibility with previous release of the library.
 *
 * @type {webpack}
 */
const webpack = require('webpack');
const path = require('path');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const packageVersion = require("./package.json").version;

module.exports = env => {
  // determine which mode
  const isProduction = env.production;
  const srcDir = path.resolve(__dirname, './src');
  const publicDir = path.resolve(__dirname, '../public');
  const libraryName = 'atk';
  const filename = isProduction ? libraryName + 'js-ui.min.js' : libraryName + 'js-ui.js';

  const prodPerformance = {
    hints: false,
    maxEntrypointSize: 512000,
    maxAssetSize: 512000
  };


  return {
    entry: srcDir + '/agile-toolkit-ui.js',
    mode: isProduction ? 'production' : 'development',
    devtool: isProduction ? false : 'source-map',
    performance: isProduction ? prodPerformance : {},
    output: {
      path: publicDir,
      filename: filename,
      library: libraryName,
      libraryTarget: 'umd',
      libraryExport: 'default',
      umdNamedDefine: true,
    },
    optimization: {
      minimizer: [new UglifyJsPlugin()]
    },
    module: {
      rules: [
        {
          test: /(\.jsx|\.js)$/,
          loader: 'babel-loader',
          exclude: /(node_modules|bower_components)/
        }
      ]
    },
    externals: {jquery: 'jQuery', draggable: 'Draggable'},
    resolve: {
      alias: {'vue$' : 'vue/dist/vue.esm.js'},
      modules: [
        path.resolve('./src'),
        path.join(__dirname, 'node_modules')
      ],
      extensions: [
        '.json',
        '.js'
      ],
    },
    plugins: [
      new webpack.DefinePlugin({
        _ATKVERSION_ : JSON.stringify(packageVersion)
      })
    ]
  };
};
