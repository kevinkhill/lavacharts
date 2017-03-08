/* jshint undef: true */
/* globals module, require */
'use strict';

/**
 * Errors module
 *
 * @module    lava/Errors
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
var LavachartsError = function (message) {
    this.name = 'LavachartsError';
    this.message = (message || '');
};
LavachartsError.prototype = Error.prototype;

/**
 * InvalidCallback Error
 *
 * thrown when when anything but a function is given as a callback
 * @type {function}
 */
var InvalidCallback = function (callback) {
    this.name = 'InvalidCallback';
    this.message = '[Lavacharts] ' + typeof callback + ' is not a valid callback.';
};
InvalidCallback.prototype = LavachartsError.prototype;
module.exports.InvalidCallback = InvalidCallback;

/**
 * InvalidLabel Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */
module.exports.InvalidLabel = function (label) {
    this.name = 'InvalidLabel';
    this.message = '[Lavacharts] "' + typeof label + '" is not a valid label.';
};
module.exports.InvalidLabel.prototype = Error.prototype;

/**
 * ElementIdNotFound Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */
module.exports.ElementIdNotFound = function (elemId) {
    this.name = 'ElementIdNotFound';
    this.message = '[Lavacharts] DOM node #' + elemId + ' was not found.';
};
module.exports.ElementIdNotFound.prototype = Error.prototype;

/**
 * ChartNotFound Error
 *
 * Thrown when when the getChart() method cannot find a chart with the given label.
 *
 * @type {function}
 */
module.exports.ChartNotFound = function (label) {
    this.name = 'ChartNotFound';
    this.message = '[Lavacharts] Chart with label "' + label + '" was not found.';
};
module.exports.ChartNotFound.prototype = Error.prototype;

/**
 * DashboardNotFound Error
 *
 * Thrown when when the getDashboard() method cannot find a chart with the given label.
 *
 * @type {function}
 */
module.exports.DashboardNotFound = function (label) {
    this.name = 'DashboardNotFound';
    this.message = '[Lavacharts] Dashboard with label "' + label + '" was not found.';
};
module.exports.DashboardNotFound.prototype = Error.prototype;
