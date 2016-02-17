"use strict";

/**
 * LavaChart object.
 *
 * @constructor
 */
module.exports = function (type, label) {
  this.type    = type;
  this.label   = label;
  this.element = null;
  this.data    = null;
  this.chart   = null;
  this.options = null;
  this.formats = [];
  this._errors = require('./errors.js')

  this.render  = lava._.noop();
  this.setData = function (data) {
    this.data = new window.google.visualization.DataTable(data, lava.dataTableVersion);
  };
  this.setElement = function (elemId) {
    this.element = document.getElementById(elemId);

    if (! this.element) {
      throw this._errors.ELEMENT_ID_NOT_FOUND(elemId);
    }
  };
  this.redraw = function() {
    this.chart.draw(this.data, this.options);
  };
  this.applyFormats = function (formatArr) {
    for(var a=0; a < formatArr.length; a++) {
      var formatJson = formatArr[a];
      var formatter = new google.visualization[formatJson.type](formatJson.config);

      formatter.format(this.data, formatJson.index);
    }
  };
};
