/**
 * Webpack v4 configuration file.
 *
 * Use mode from env variable pass to webpack in order to
 * differentiate build mode.
 * Use a function that return configuration object based
 * on env variable.
 *
 * Using Development
 * - set webpack config mode to development
 *
 * Using Production
 * - set webpack config mode to production
 * - change name of output file by adding .min
 *
 * Module export will output default value
 * using libraryExport: 'default' for backward
 * compatibility with previous release of the library.
 */
const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env) => {
    // determine which mode
    const isProduction = env.production;
    const srcDir = path.resolve(__dirname, './src');
    const publicDir = path.resolve(__dirname, '../public');
    const libraryName = 'atk';
    const filename = libraryName + 'js-ui';

    const prodPerformance = {
        hints: false,
        maxEntrypointSize: 640 * 1024,
        maxAssetSize: 640 * 1024,
    };

    return {
        entry: { [filename]: srcDir + '/main.js' },
        mode: isProduction ? 'production' : 'development',
        devtool: 'source-map',
        performance: isProduction ? prodPerformance : {},
        output: {
            path: publicDir,
            filename: isProduction ? 'js/[name].min.js' : 'js/[name].js',
            library: libraryName,
            libraryTarget: 'umd',
            libraryExport: 'default',
            umdNamedDefine: true,
        },
        optimization: {
            splitChunks: {
                cacheGroups: {
                    vendorVueFlatpickr: {
                        test: /[\\/]node_modules[\\/](flatpickr|vue-flatpickr-component)[\\/]/,
                        name: 'vendor-vue-flatpickr',
                    },
                    vendorVueQueryBuilder: {
                        test: /[\\/]node_modules[\\/]vue-query-builder[\\/]/,
                        name: 'vendor-vue-query-builder',
                    },
                    vendorVue: {
                        test: /[\\/]node_modules[\\/](?!(vue-flatpickr-component|vue-query-builder)[\\/])([^\\/]+[-.])?vue([-.][^\\/]+)?[\\/]/,
                        name: 'vendor-vue',
                    },
                    vendor: {
                        test: /[\\/]node_modules[\\/](?!(([^\\/]+[-.])?vue([-.][^\\/]+)?|flatpickr)[\\/])/,
                        name: 'vendor',
                    },
                },
            },
            minimizer: [
                new TerserPlugin({
                    terserOptions: {
                        output: {
                            comments: false,
                        },
                    },
                    extractComments: false,
                }),
            ],
        },
        module: {
            rules: [
                {
                    test: /(\.js|\.jsx)$/,
                    enforce: 'pre',
                    loader: 'source-map-loader',
                },
                {
                    test: /(\.js|\.jsx)$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/,
                },
                // load .vue file
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                },
                // this will apply to both plain .css files
                // AND <style> blocks in .vue files
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        'style-loader',
                        'css-loader',
                    ],
                },
            ],
        },
        externals: { 'external/jquery': 'jQuery' },
        resolve: {
            alias: {
                atk$: srcDir + '/setup-atk.js',
                vue$: 'vue/dist/vue.esm.js',
            },
            modules: [
                srcDir,
                'node_modules',
            ],
            extensions: [
                '.json',
                '.js',
            ],
        },
        plugins: [
            new VueLoaderPlugin(),
        ],
    };
};
