"use strict";

/**
 * Chart.js
 *
 * @constructor
 */
var Chart = function (type, label) {
  this.type     = type;
  this.label    = label;
  this.element  = null;
  this.data     = null;
  this.chart    = null;
  this.options  = null;
  this.formats  = [];
  this.render   = null;
  this._errors  = require('./Errors.js');
};

Chart.prototype.setData = function (data) {
  this.data = new window.google.visualization.DataTable(data, lava.dataVer);
};

Chart.prototype.setOptions = function (options) {
  this.options = options;
};

Chart.prototype.setElement = function (elemId) {
  this.element = document.getElementById(elemId);

  if (! this.element) {
    throw this._errors.ELEMENT_ID_NOT_FOUND(elemId);
  }
};

Chart.prototype.redraw = function() {
  this.chart.draw(this.data, this.options);
};

Chart.prototype.applyFormats = function (formatArr) {
  for(var a=0; a < formatArr.length; a++) {
    var formatJson = formatArr[a];
    var formatter = new google.visualization[formatJson.type](formatJson.config);

    formatter.format(this.data, formatJson.index);
  }
};

module.exports = Chart;
