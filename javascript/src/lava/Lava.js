/* jshint undef: true, unused: true */
/* globals window, document, console, google, module, require */

/**
 * lava.js module
 *
 * @module    lava/Lava
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   http://opensource.org/licenses/MIT MIT
 */
module.exports = (function() {
    'use strict';

    var Q = require('q');
    // var Promise = require('bluebird');
    var _ = require('lodash');
    var util = require('util');
    var EventEmitter = require('events');

    function Lava() {
        /**
         * Defining the Chart class within the module.
         *
         * @type {Chart}
         */
        this.Chart = require('./Chart.js');

        /**
         * Defining the Dashboard class within the module.
         *
         * @type {Dashboard}
         */
        this.Dashboard = require('./Dashboard.js');

        /**
         * Urls to Google's static loader
         *
         * @type {string}
         * @public
         */
        this.gstaticUrl = 'https://www.gstatic.com/charts/loader.js';

        /**
         * JSON object of config items.
         *
         * @type {Object}
         * @private
         */
        this.options = (function () {
            if (typeof OPTIONS_JSON !== 'object') {
                return {};
            }

            return OPTIONS_JSON;
        }());

        /**
         * Array of charts stored in the module.
         *
         * @type {Array.<Chart>}
         * @private
         */
        this._charts = [];

        /**
         * Array of dashboards stored in the module.
         *
         * @type {Array.<Dashboard>}
         * @private
         */
        this._dashboards = [];

        /**
         * Ready callback to be called when the module is finished running.
         *
         * @callback _readyCallback
         * @private
         */
        this._readyCallback = _.noop();

        /**
         * Error definitions for the module.
         *
         * @private
         */
        this._errors = require('./Errors.js');

        /**
         * Apply the EventEmitter methods to Lava
         */
        EventEmitter.call(this);
    }

    /**
     * Inherit from the EventEmitter
     */
    util.inherits(Lava, EventEmitter);

    /**
     * Initialize the Lava.js module by attaching the event listeners
     * and calling the charts' and dashboards' init methods
     *
     * @public
     */
    Lava.prototype.init = function () {
        console.log('[lava.js] Initializing');

        var $lava = this;
        var readyCount = 0;

        this.on('ready', function (renderable) {
            console.log('[lava.js] ' + renderable.uuid() + ' -> ready');

            readyCount++;

            if (readyCount === $lava._getRenderables().length) {
                console.log('[lava.js] Loading Google');

                $lava._loadGoogle().then(function() {
                    return $lava._mapRenderables(function (renderable) {
                        console.log('[lava.js] ' + renderable.uuid() + ' -> configuring');

                        return renderable.configure();
                    });
                }).then(function() {
                    return $lava._mapRenderables(function (renderable) {
                        console.log('[lava.js] ' + renderable.uuid() + ' -> rendering');

                        return renderable.render();
                    });
                }).then(function() {
                    console.log('[lava.js] Ready, firing ready callback');

                    $lava._readyCallback();
                });
            }
        });
    };

    /**
     * Runs the Lava.js module by calling all the renderables' init methods
     *
     * @public
     */
    Lava.prototype.run = function () {
        this.init();

        this._forEachRenderable(function (renderable) {
            console.log('[lava.js] ' + renderable.uuid() + ' -> initializing');

            renderable.init();
        });
    };

    /**
     * Stores a renderable lava object within the module.
     *
     * @param {Chart|Dashboard} renderable
     */
    Lava.prototype.store = function (renderable) {
        if (renderable instanceof this.Chart) {
            this.storeChart(renderable);
        }

        if (renderable instanceof this.Dashboard) {
            this.storeDashboard(renderable);
        }
    };

    /**
     * Assigns a callback for when the charts are ready to be interacted with.
     *
     * This is used to wrap calls to lava.loadData() or lava.loadOptions()
     * to protect against accessing charts that aren't loaded yet
     *
     * @public
     * @param {Function} callback
     */
    Lava.prototype.ready = function (callback) {
        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this._readyCallback = callback;
    };

    /**
     * Event wrapper for chart events.
     *
     *
     * Used internally when events are applied so the user event function has
     * access to the chart within the event callback.
     *
     * @param {Object} event
     * @param {Object} lavachart
     * @param {Function} callback
     * @return {Function}
     */
    Lava.prototype.event = function (event, lavachart, callback) {
        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        return callback(event, lavachart.chart, lavachart.data);
    };

    /**
     * Loads new data into the chart and redraws.
     *
     *
     * Used with an AJAX call to a PHP method returning DataTable->toJson(),
     * a chart can be dynamically update in page, without reloads.
     *
     * @public
     * @param {string} label
     * @param {string} json
     * @param {Function} callback
     */
    Lava.prototype.loadData = function (label, json, callback) {
        if (typeof callback === 'undefined') {
            callback = _.noop;
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this.getChart(label, function (chart) {
            if (typeof json.data !== 'undefined') {
                chart.setData(json.data);
            } else {
                chart.setData(json);
            }

            if (typeof json.formats !== 'undefined') {
                chart.applyFormats(json.formats);
            }

            chart.redraw();

            callback(chart);
        });
    };

    /**
     * Loads new options into a chart and redraws.
     *
     *
     * Used with an AJAX call, or javascript events, to load a new array of options into a chart.
     * This can be used to update a chart dynamically, without reloads.
     *
     * @public
     * @param {string} label
     * @param {string} json
     * @param {Function} callback
     */
    Lava.prototype.loadOptions = function (label, json, callback) {
        if (typeof callback === 'undefined') {
            callback = callback || _.noop;
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this.getChart(label, function (chart) {
            chart.setOptions(json);

            chart.redraw();

            callback(chart);
        });
    };

    /**
     * Redraws all of the registered charts on screen.
     *
     * This method is attached to the window resize event with a 300ms debounce
     * to make the charts responsive to the browser resizing.
     */
    Lava.prototype.redrawCharts = function() {
        this._forEachRenderable(function (renderable) {
            console.log('[lava.js] ' + renderable.uuid() + ' -> redrawing');

            var redraw = _.bind(renderable.redraw, renderable);

            redraw();
        });
    };

    /**
     * Create a new Chart.
     *
     * @public
     * @param  {string} type Type of chart to create
     * @param  {string} label Label for the chart
     * @return {Chart}
     */
    Lava.prototype.createChart = function (type, label) {
        return new this.Chart(type, label);
    };

    /**
     * Stores a chart within the module.
     *
     * @public
     * @param {Chart} chart
     */
    Lava.prototype.storeChart = function (chart) {
        this._charts.push(chart);
    };

    /**
     * Returns the LavaChart javascript objects
     *
     *
     * The LavaChart object holds all the user defined properties such as data, options, formats,
     * the GoogleChart object, and relative methods for internal use.
     *
     * The GoogleChart object is available as ".chart" from the returned LavaChart.
     * It can be used to access any of the available methods such as
     * getImageURI() or getChartLayoutInterface().
     * See https://google-developers.appspot.com/chart/interactive/docs/gallery/linechart#methods
     * for some examples relative to LineCharts.
     *
     * @public
     * @param  {string}   label
     * @param  {Function} callback
     * @throws InvalidLabel
     * @throws InvalidCallback
     * @throws ChartNotFound
     */
    Lava.prototype.getChart = function (label, callback) {
        if (typeof label !== 'string') {
            throw new this._errors.InvalidLabel(label);
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        var chart = _.find(this._charts, {label: label});

        if (!chart) {
            throw new this._errors.ChartNotFound(label);
        }

        callback(chart);
    };

    /**
     * Create a new Dashboard with a given label.
     *
     * @public
     * @param  {string} label
     * @return {Dashboard}
     */
    Lava.prototype.createDashboard = function (label) {
        return new this.Dashboard(label);
    };

    /**
     * Stores a dashboard within the module.
     *
     * @public
     * @param {Dashboard} dash
     */
    Lava.prototype.storeDashboard = function (dash) {
        this._dashboards.push(dash);
    };

    /**
     * Retrieve a Dashboard from Lava.js
     *
     * @public
     * @param  {string}   label    Dashboard label
     * @param  {Function} callback Callback function
     * @throws InvalidLabel
     * @throws InvalidCallback
     * @throws DashboardNotFound
     */
    Lava.prototype.getDashboard = function (label, callback) {
        if (typeof label !== 'string') {
            throw new this._errors.InvalidLabel(label);
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        var dash = _.find(this._dashboards, {label: label});

        if (dash instanceof this.Dashboard === false) {
            throw new this._errors.DashboardNotFound(label);
        }

        callback(dash);
    };

    /**
     * Returns an array with the charts and dashboards.
     *
     * @private
     * @return {Array}
     */
    Lava.prototype._getRenderables = function () {
        return _.concat(this._charts, this._dashboards);
    };

    /**
     * Applies the callback to each of the charts and dashboards.
     *
     * @private
     * @param {Function} callback
     */
    Lava.prototype._forEachRenderable = function (callback) {
        _.forEach(this._getRenderables(), callback);
    };

    /**
     * Applies the callback and builds an array of return values
     * for each of the charts and dashboards.
     *
     * @private
     * @param {Function} callback
     * @return {Array}
     */
    Lava.prototype._mapRenderables = function (callback) {
        return _.map(this._getRenderables(), callback);
    };

    /**
     * Returns an array of the google packages to load.
     *
     * @private
     * @return {Array}
     */
    Lava.prototype._getPackages = function () {
        return _.union(
            _.map(this._charts, 'package'),
            _.flatten(_.map(this._dashboards, 'packages'))
        );
    };

    /**
     * Check if Google's Static Loader is in page.
     *
     * @private
     * @returns {boolean}
     */
    Lava.prototype._googleIsLoaded = function () {
        var scripts = document.getElementsByTagName('script');
        var loaded = false;

        for (var i = scripts.length; i--;) {
            if (scripts[i].src === this.gstaticUrl) {
                loaded = true;
            }
        }

        return loaded;
    };

    /**
     * Load the Google Static Loader and resolve the promise when ready.
     *
     * @private
     * @returns {Promise}
     */
    Lava.prototype._loadGoogle = function () {
        var $lava = this;
        var deferred = Q.defer();
        var script = this._createScriptTag(deferred);

        if (this._googleIsLoaded()) {
            console.log('[lava.js] Static loader found, initializing window.google');

            $lava._googleChartLoader(deferred);
        } else {
            console.log('[lava.js] Static loader not found, appending to head');

            document.head.appendChild(script);
        }

        return deferred.promise;
    };

    /**
     * Create a new script tag for the Google Static Loader.
     *
     * @private
     * @param {Promise} deferred
     * @returns {Element}
     */
    Lava.prototype._createScriptTag = function (deferred) {
        var script = document.createElement('script');
        var $lava = this;

        script.type = 'text/javascript';
        script.async = true;
        script.src = this.gstaticUrl;
        script.onload = script.onreadystatechange = function (event) {
            event = event || window.event;

            if (event.type === 'load' || (/loaded|complete/.test(this.readyState))) {
                this.onload = this.onreadystatechange = null;

                $lava._googleChartLoader(deferred);
            }
        };

        return script;
    };

    /**
     * Runs the Google chart loader and resolves the promise.
     *
     * @param {Promise} deferred
     * @private
     */
    Lava.prototype._googleChartLoader = function (deferred) {
        var config = {
            packages: this._getPackages(),
            language: this.options.locale
        };

        if (this.options.maps_api_key !== '') {
            config.mapsApiKey = this.options.maps_api_key;
        }

        console.log('[lava.js] Google loaded with options:', config);

        google.charts.load('current', config);

        google.charts.setOnLoadCallback(deferred.resolve);
    };

    return new Lava();
}());
