"use strict";

const webpack = require("webpack");
const baseConfig = require("./webpack.config.base.js");
const MinifyPlugin = require("babel-minify-webpack-plugin");

module.exports = function () {
    baseConfig.devtool = "source-map";

    let typeScriptRule = baseConfig.module.rules[0];
    typeScriptRule.use[0].options.presets.push(
        ["env", {
            "targets": {
                "browsers": ["last 2 versions"]
            }
        }],
    );

    baseConfig.plugins.push(
        new webpack.DefinePlugin({
            "process.env": {"NODE_ENV": JSON.stringify("production")},
        }),

        new MinifyPlugin({
            removeConsole: true,
            removeDebugger: true,
        }),

        // new webpack.optimize.CommonsChunkPlugin({
        //     name: "inline",
        //     filename: "inline.js",
        //     minChunks: Infinity
        // }),
        // new webpack.optimize.AggressiveSplittingPlugin({
        //     minSize: 5000,
        //     maxSize: 10000
        // }),
    );

    return baseConfig;
};
