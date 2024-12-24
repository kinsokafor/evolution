const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");
const Dotenv = require("dotenv-webpack");
const webpack = require('webpack');
const path = require('path');

module.exports = merge(common, {
  mode: "development",
  devtool: "inline-source-map",
  devServer: {
    static: {
      directory: path.resolve(__dirname, 'Public'), // Serve all static files from Public
      publicPath: '/', // Serve assets from /dist/
    },
    watchFiles: {
      paths: ['./Public/dist/**/*', './EvoJsDev/**/*'], // Watch assets and source files
      options: {
        usePolling: true, // Useful for environments where file changes aren't detected
      },
    },
    proxy: {
      "/": {
        target: "http://localhost:3005", // Proxy requests to PHP server
        changeOrigin: true,
      },
    },
    hot: true, // Enable hot module replacement
    liveReload: true, // Reload the browser on file changes
    open: true, // Automatically open the browser
    port: 8080, // DevServer's port for bundled assets (can be changed)
    historyApiFallback: true, // Support SPA routing if needed
  },
  plugins: [
    new Dotenv({
      path: "./.env.development",
    }),
    new webpack.HotModuleReplacementPlugin(),
  ].filter(Boolean),
});

