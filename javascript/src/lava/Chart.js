/* jshint undef: true */
/* globals document, google, require, module */

/**
 * Chart class used for storing all the needed configuration for rendering.
 *
 * @typedef {Function} Chart
 * @property {string} label - Label for the chart.
 * @property {string} type - Type of chart.
 * @property {Object} element - Html element in which to render the chart.
 * @property {Object} chart - Google chart object.
 * @property {string} package - Type of Google chart package to load.
 * @property {boolean} pngOutput - Should the chart be displayed as a PNG.
 * @property {Object} data - Datatable for the chart.
 * @property {Object} options - Configuration options for the chart.
 * @property {Array} formats - Formatters to apply to the chart data.
 * @property {Object} promises - Promises used in the rendering chain.
 * @property {Function} init - Initializes the chart.
 * @property {Function} configure - Configures the chart.
 * @property {Function} render - Renders the chart.
 * @property {Function} uuid - Creates identification string for the chart.
 * @property {Object} _errors - Collection of errors to be thrown.
 */

/**
 * Chart module
 *
 * @class     Chart
 * @module    lava/Chart
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
module.exports = (function() {
    'use strict';

    var Q = require('q');

    /**
     * Chart Class
     *
     * This is the javascript version of a lavachart with methods for interacting with
     * the google chart and the PHP lavachart output.
     *
     * @param {string} type
     * @param {string} label
     * @constructor
     */
    function Chart (type, label) {
        this.label     = label;
        this.type      = type;
        this.element   = null;
        this.chart     = null;
        this.package   = null;
        this.pngOutput = false;
        this.data      = {};
        this.options   = {};
        this.formats   = [];
        this.promises = {
            configure: Q.defer(),
            rendered: Q.defer()
        };
        this.init      = function(){};
        this.configure = function(){};
        this.render    = function(){};
        this.uuid      = function() {
            return this.type+'::'+this.label;
        };
        this._errors = require('./Errors.js');
    }

    /**
     * Sets the data for the chart by creating a new DataTable
     *
     * @public
     * @external "google.visualization.DataTable"
     * @see   {@link https://developers.google.com/chart/interactive/docs/reference#DataTable|DataTable Class}
     * @param {object} payload Json representation of a DataTable
     */
    Chart.prototype.setData = function (payload) {
         // If a DataTable#toJson() payload is received, with formatted columns,
         // then payload.data will be defined, and used as the DataTable
        if (typeof payload.data === 'object') {
            payload = payload.data;
        }

        // Since Google compiles their classes, we can't use instanceof to check since
        // it is no longer called a "DataTable" (it's "gvjs_P" but that could change...)
        if (typeof payload.getTableProperties === 'function') {
            this.data = payload;

        // Otherwise assume it is a JSON representation of a DataTable and create one.
        } else {
            this.data = new google.visualization.DataTable(payload);
        }
    };

    /**
     * Sets the options for the chart.
     *
     * @public
     * @param {object} options
     */
    Chart.prototype.setOptions = function (options) {
        this.options = options;
    };

    /**
     * Sets whether the chart is to be rendered as PNG or SVG
     *
     * @public
     * @param {string|int} png
     */
    Chart.prototype.setPngOutput = function (png) {
        this.pngOutput = Boolean(typeof png === 'undefined' ? false : png);
    };

    /**
     * Set the ID of the output element for the Dashboard.
     *
     * @public
     * @param  {string} elemId
     * @throws ElementIdNotFound
     */
    Chart.prototype.setElement = function (elemId) {
        this.element = document.getElementById(elemId);

        if (! this.element) {
            throw new this._errors.ElementIdNotFound(elemId);
        }
    };

    /**
     * Redraws the chart.
     *
     * @public
     */
    Chart.prototype.redraw = function() {
        this.chart.draw(this.data, this.options);
    };

    /**
     * Draws the chart as a PNG instead of the standard SVG
     *
     * @public
     * @external "chart.getImageURI"
     * @see {@link https://developers.google.com/chart/interactive/docs/printing|Printing PNG Charts}
     */
    Chart.prototype.drawPng = function() {
        var img = document.createElement('img');
            img.src = this.chart.getImageURI();

        this.element.innerHTML = '';
        this.element.appendChild(img);
    };

    /**
     * Formats columns of the DataTable.
     *
     * @public
     * @param {Array.<Object>} formatArr Array of format definitions
     */
    Chart.prototype.applyFormats = function (formatArr) {
        for(var a=0; a < formatArr.length; a++) {
            var formatJson = formatArr[a];
            var formatter = new google.visualization[formatJson.type](formatJson.config);

            formatter.format(this.data, formatJson.index);
        }
    };

    return Chart;

}());
