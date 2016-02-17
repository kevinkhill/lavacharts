"use strict";

/**
 * Dashboard object.
 *
 * @constructor
 */
module.exports = function (label) {
  this.label     = label;
  this.element   = null;
  this.render    = null;
  this.data      = null;
  this.bindings  = [];
  this.dashboard = null;
  this._errors   = require('./errors.js')

  this.setElement = function (elemId) {
    this.element = document.getElementById(elemId);

    if (! this.element) {
      throw this._errors.ELEMENT_ID_NOT_FOUND(elemId);
    }
  };
};
