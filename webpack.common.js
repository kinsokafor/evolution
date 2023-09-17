const {entryPoints} = require('./evo.config');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');

module.exports = {
    resolve: {
        fallback: {
          "url": false,
        },
        alias: {
            '@': path.resolve(__dirname, 'EvoJsDev/'),
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
                test: /\.(png|jpe?g|gif|svg)$/i,
                loader: 'file-loader',
                options: {
                    publicPath: '/Public/dist/images/',
                    outputPath: 'images',
                }
            },
        ]
    },
}