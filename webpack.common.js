const {entryPoints} = require('./evo.config');
const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const jQueryPath = 'jquery/dist/jquery.js'

module.exports = {
    resolve: {
        fallback: {
          "url": false,
        },
        alias: {
            '@root': path.resolve(__dirname, './'),
            '@': path.resolve(__dirname, 'EvoJsDev/'),
            '@components': path.resolve(__dirname, 'EvoJsDev/components/'),
            '@form': path.resolve(__dirname, 'EvoJsDev/components/form/'),
            '@theme': path.resolve(__dirname, 'EvoJsDev/components/theme/'),
            '@filter': path.resolve(__dirname, 'EvoJsDev/components/filters/'),
            '@menu': path.resolve(__dirname, 'EvoJsDev/components/menu/'),
            '@module': path.resolve(__dirname, 'EvoJsDev/Modules/'),
            '@main': path.resolve(__dirname, 'EvoJsDev/Modules/Main/'),
            'jquery$': jQueryPath,
        }
    },
    entry: entryPoints(),
    output: {
        path: path.resolve(__dirname, 'Public/dist'),
        filename: () => {
            return '[name]__[contenthash].js'
        },
        publicPath: '/',
        clean: true,
    },
    plugins: [
        new HtmlWebpackPlugin({
            filename: 'all-scripts.html',
            templateContent: '',
        }),
        new VueLoaderPlugin(),
        new webpack.ProvidePlugin({     
            jQuery: jQueryPath,
            $: jQueryPath,
            'window.jQuery': jQueryPath,
        })
    ].filter(Boolean),
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: ['style-loader', 'css-loader', 'sass-loader']
            },
            {
                test: /\.vue$/,
                use: ['vue-loader']
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            },
            {
                test: /\.(png|jpe?g|gif|svg|webp)$/i,
                loader: 'file-loader',
                options: {
                    publicPath: '/Public/dist/images/',
                    outputPath: 'images',
                }
            },
        ]
    },
}