"use strict";

/**
 * Dashboard object.
 *
 * @constructor
 */
var Dashboard = function (label) {
  this.label     = label;
  this.element   = null;
  this.render    = null;
  this.bindings  = [];
  this.dashboard = null;
  this._data     = null;
  this._errors   = require('./errors.js');
};

Dashboard.prototype.setData = function (data) {
  this._data = new window.google.visualization.DataTable(data, lava.dataVer);
};

Dashboard.prototype.setElement = function (elemId) {
  this.element = document.getElementById(elemId);

  if (! this.element) {
    throw this._errors.ELEMENT_ID_NOT_FOUND(elemId);
  }
};

module.exports = Dashboard;
