'use strict';

let baseConfig = require('./base.config.js');

baseConfig.mode = 'development';
baseConfig.devtool = "inline-source-map";

module.exports = baseConfig;
