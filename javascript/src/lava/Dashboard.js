/* jshint undef: true */
/* globals document, google, require, module */

/**
 * Dashboard module
 *
 * @class     Dashboard
 * @module    lava/Dashboard
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
module.exports = (function() {
    'use strict';

    var Q = require('q');

    /**
     * Dashboard Class
     *
     * This is the javascript version of a dashboard with methods for interacting with
     * the google chart and the PHP lavachart output.
     *
     * @param {String} label
     * @constructor
     */
    function Dashboard (label) {
        this.label     = label;
        this.type      = 'Dashboard';
        this.element   = null;
        this.data      = null;
        this.bindings  = [];
        this.dashboard = null;
        this.deferred  = Q.defer();
        this.init      = function(){};
        this.configure = function(){};
        this.render    = function(){};
        this.uuid      = function() {
            return this.type+'::'+this.label;
        };
        this._errors   = require('./Errors.js');
    }

    /**
     * Sets the data for the chart by creating a new DataTable
     *
     * @external "google.visualization.DataTable"
     * @see   {@link https://developers.google.com/chart/interactive/docs/reference#DataTable|DataTable}
     * @param {Object}        data      Json representation of a DataTable
     * @param {Array.<Array>} data.cols Array of column definitions
     * @param {Array.<Array>} data.rows Array of row definitions
     */
    Dashboard.prototype.setData = function (data) {
        this.data = new google.visualization.DataTable(data);
    };

    /**
     * Set the ID of the output element for the Dashboard.
     *
     * @public
     * @param  {string} elemId
     * @throws ElementIdNotFound
     */
    Dashboard.prototype.setElement = function (elemId) {
        this.element = document.getElementById(elemId);

        if (! this.element) {
            throw new this._errors.ElementIdNotFound(elemId);
        }
    };

    return Dashboard;

}());
