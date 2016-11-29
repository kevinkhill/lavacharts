/* jshint undef: true */
/* globals module, require */
'use strict';

/**
 * Errors module
 *
 * @module    lava/Errors
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @license   MIT
 */
var ce = require('node-custom-errors');
var LavachartsError = ce.create({
    name: 'LavachartsError',
    abstract: true
});

/**
 * InvalidCallback Error
 *
 * thrown when when anything but a function is given as a callback
 * @type {function}
 */
module.exports.InvalidCallback = ce.create({
    name: 'InvalidCallback',
    parent: LavachartsError,
    construct: function (callback) {
        this.message = '[Lavacharts] ' + typeof callback + ' is not a valid callback.';
    }
});

/**
 * InvalidLabel Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */
module.exports.InvalidLabel = ce.create({
    name: 'InvalidLabel',
    parent: LavachartsError,
    construct: function (label) {
        this.message = '[Lavacharts] "' + typeof label + '" is not a valid label.';
    }
});

/**
 * ElementIdNotFound Error
 *
 * Thrown when when anything but a string is given as a label.
 *
 * @type {function}
 */
module.exports.ElementIdNotFound = ce.create({
    name: 'ElementIdNotFound',
    parent: LavachartsError,
    construct: function (elemId) {
        this.message = '[Lavacharts] DOM node #' + elemId + ' was not found.';
    }
});

/**
 * ChartNotFound Error
 *
 * Thrown when when the getChart() method cannot find a chart with the given label.
 *
 * @type {function}
 */
module.exports.ChartNotFound = ce.create({
    name: 'ChartNotFound',
    parent: LavachartsError,
    construct: function (label) {
        this.message = '[Lavacharts] Chart with label "' + label + '" was not found.';
    }
});

/**
 * DashboardNotFound Error
 *
 * Thrown when when the getDashboard() method cannot find a chart with the given label.
 *
 * @type {function}
 */
module.exports.DashboardNotFound = ce.create({
    name: 'DashboardNotFound',
    parent: LavachartsError,
    construct: function (label) {
        this.message = '[Lavacharts] Dashboard with label "' + label + '" was not found.';
    }
});
