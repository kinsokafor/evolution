const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");
const webpack = require('webpack');
const config = require('./config.json');
const Dotenv = require('dotenv-webpack');
module.exports = merge(common, {
  mode: "development",
  devtool: "inline-source-map",
  plugins: [
      new Dotenv({
        path: './.env.development'
      }),
      new webpack.DefinePlugin({
        'process.env.EVO_API_URL': JSON.stringify(config.devRoot),
      }),
  ].filter(Boolean),
});