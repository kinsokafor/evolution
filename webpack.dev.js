const { merge } = require("webpack-merge");
const common = require("./webpack.common.js");
const webpack = require("webpack");
const config = require("./config.json");
const Dotenv = require("dotenv-webpack");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");
module.exports = merge(common, {
  mode: "development",
  devtool: "inline-source-map",
  plugins: [
    new Dotenv({
      path: "./.env.development",
    }),
    new BrowserSyncPlugin(
      {
        host: "localhost",
        port: 3000,
        proxy: "http://localhost:3005", // Your PHP server
        files: [
          "Public/**/*.php", // Watch PHP files for changes
          "Public/**/*.html", // Watch HTML files
        ],
        open: true,
        notify: false, // Disable browser notifications
        ghostMode: false,
        cookies: {
          stripDomain: false, // Ensures cookies are forwarded correctly
          secure: false, // Allow cookies over HTTP
          sameSite: "Lax", // Set SameSite attribute to 'Lax'
          path: "/", // Make cookies available for all paths
          // domain: "example.com", // Specify a domain for the cookie
          httpOnly: true,
        },
      },
      {
        reload: false, // Prevent double reloading from Webpack and Browsersync
      }
    ),
  ].filter(Boolean),
});
