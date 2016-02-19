"use strict";

/**
 * Errors.js
 *
 * Author:  Kevin Hill
 * Email:   kevinkhill@gmail.com
 * Github:  https://github.com/kevinkhill/lavacharts
 * License: MIT
 */
module.exports = {
  INVALID_CALLBACK : function (callback) {
    return new Error('[Lavacharts] ' + typeof callback + ' is not a valid callback.');
  },
  INVALID_LABEL : function (label) {
    return new Error('[Lavacharts] ' + typeof label + ' is not a valid label.');
  },
  ELEMENT_ID_NOT_FOUND : function (elemId) {
    return new Error('[Lavacharts] DOM node #' + elemId + ' was not found.');
  },
  CHART_NOT_FOUND : function (label) {
    return new Error('[Lavacharts] Chart with label "' + label + '" was not found.');
  },
  DASHBOARD_NOT_FOUND : function (label) {
    return new Error('[Lavacharts] Dashboard with label "' + label + '" was not found.');
  }
};
