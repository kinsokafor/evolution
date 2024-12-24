const { entryPoints } = require('./evo.config');
const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const jQueryPath = 'jquery/dist/jquery.js';
const entries = entryPoints();

module.exports = {
  resolve: {
    fallback: {
      url: false,
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
    },
  },
  entry: entries,
  output: {
    path: path.resolve(__dirname, 'Public/dist'),
    filename: '[name]__[contenthash].js',
    publicPath: '/', // Ensure this matches devServer publicPath
    clean: true,
  },
  cache: {
    type: 'filesystem',
  },
  plugins: [
    ...Object.keys(entries).map((entryName) => {
      return new HtmlWebpackPlugin({
        filename: `${entryName}.html`, // Generate a unique HTML file per entry
        templateContent: '',
        chunks: [entryName], // Include only the corresponding chunk
      });
    }),
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin({
      filename: '[name]__[contenthash].css',
    }),
    new webpack.ProvidePlugin({
      jQuery: jQueryPath,
      $: jQueryPath,
      'window.jQuery': jQueryPath,
    }),
  ],
  optimization: {
    splitChunks: {
      chunks: 'all',
      cacheGroups: {
        defaultVendors: {
          test: /[\\/]node_modules[\\/]/, // Target vendor modules
          name: 'vendors',
          chunks: 'all',
          priority: -10,
          enforce: true,
        },
        common: {
          name: 'common',
          minChunks: 2, // Modules shared by at least two entry points
          priority: -20,
          reuseExistingChunk: true,
        },
      },
    },
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
      },
      {
        test: /\.vue$/,
        use: ['vue-loader'],
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
      {
        test: /\.(png|jpe?g|gif|svg|webp)$/i,
        type: 'asset/resource',
        generator: {
          filename: 'images/[hash][ext][query]',
          publicPath: '/dist/',
        },
      },
      {
        test: /\.mp3$/,
        type: 'asset/resource',
        generator: {
          filename: 'sounds/[hash][ext][query]',
          publicPath: '/dist/',
        },
      },
    ],
  },
};
