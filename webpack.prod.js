const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");
const webpack = require('webpack');
const config = require('./config.json');
const Dotenv = require('dotenv-webpack');
module.exports = merge(common, {
  mode: "production",
  plugins: [
    new Dotenv({
      path: './.env.production'
    }),
  ].filter(Boolean),
});