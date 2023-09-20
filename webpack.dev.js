const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");
const webpack = require('webpack');
const config = require('./config.json');
module.exports = merge(common, {
  mode: "development",
  devtool: "inline-source-map",
  plugins: [
      new webpack.EnvironmentPlugin({
          EVO_API_URL: config.devRoot,
          __VUE_PROD_DEVTOOLS__: true,
          __VUE_OPTIONS_API__: true
      })
  ].filter(Boolean),
});