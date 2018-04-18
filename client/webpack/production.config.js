'use strict';

let baseConfig = require('./base.config.js');

baseConfig.mode = 'production';
baseConfig.devtool = 'source-map';

module.exports = baseConfig;
