"use strict";

let baseConfig = require("./webpack.config.base.js");

module.exports = function() {
    baseConfig.devtool = "inline-source-map";

    return baseConfig;
};
