<<<<<<< HEAD
/* jshint undef: true, unused: true */
/* globals window, document, console, google, module, require */

/**
 * Lava module
 *
 * @module    lava/Lava
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @license   MIT
 */
module.exports = (function() {
    'use strict';

    var Q = require('q');
    var _ = require('lodash');
    var util = require('util');
    var EventEmitter = require('events');

    function Lava() {
        /**
         * Setting the debug flag
         *
         * @type {boolean}
         */
        this._debug = true;

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
         * Urls to Google's resources
         *
         * @type {{jsapi: string, gstatic: string}}
         */
        this.urls = {
            jsapi:   'https://www.google.com/jsapi',
            gstatic: 'https://www.gstatic.com/charts/loader.js'
        };

        /**
         * Array of config items.
         *
         * @type {Array}
         * @private
         */
        this._config = CONFIG_JSON;

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
     * Stores a renderable lava object within the module.
     *
     * @param {Chart|Dashboard} renderable
     */
    Lava.prototype.store = function (renderable) {
        if (renderable instanceof lava.Chart) {
            this.storeChart(renderable);
        }

        if (renderable instanceof lava.Dashboard) {
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
     * @param {Object} chart
     * @param {Function} callback
     * @return {Function}
     */
    Lava.prototype.event = function (event, chart, callback) {
        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        return callback(event, chart);
    };

    /**
     * Loads new data into the chart and redraws.
     *
     *
     * Used with an AJAX call to a PHP method returning DataTable->toJson(),
     * a chart can be dynamically update in page, without reloads.
     *
     * @public
     * @param {String} label
     * @param {String} json
     * @param {Function} callback
     */
    Lava.prototype.loadData = function (label, json, callback) {
        if (typeof callback == 'undefined') {
            callback = _.noop;
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this.getChart(label, function (chart) {
            if (typeof json.data != 'undefined') {
                chart.setData(json.data);
            } else {
                chart.setData(json);
            }

            if (typeof json.formats != 'undefined') {
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
     * @param {String} label
     * @param {String} json
     * @param {Function} callback
     */
    Lava.prototype.loadOptions = function (label, json, callback) {
        if (typeof callback == 'undefined') {
            callback = _.noop;
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
    Lava.prototype.redrawCharts = function () {
        _.debounce(_.bind(function () {
            this._forEachRenderable(function (renderable) {
                renderable.redraw();
            });
        }, this), 300);
    };

    /**
     * Create a new Chart.
     *
     * @public
     * @param  {String} type Type of chart to create
     * @param  {String} type Label for the chart
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
     * @param  {String}   label
     * @param  {Function} callback
     * @throws InvalidLabel
     * @throws InvalidCallback
     * @throws ChartNotFound
     */
    Lava.prototype.getChart = function (label, callback) {
        if (typeof label != 'string') {
            throw new this._errors.InvalidLabel(label);
        }

        if (typeof callback != 'function') {
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
     * @param  {String} label
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
     * @param  {String}   label    Dashboard label.
     * @param  {Function} callback Callback function
     * @throws InvalidLabel
     * @throws InvalidCallback
     * @throws DashboardNotFound
     */
    Lava.prototype.getDashboard = function (label, callback) {
        if (typeof label != 'string') {
            throw new this._errors.InvalidLabel(label);
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        var dash = _.find(this._dashboards, {label: label});

        if (dash instanceof lava.Dashboard === false) {
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
     * Returns the defined locale of the charts.
     *
     * @private
     * @return {String}
     */
    Lava.prototype._getLocale = function () {
        return this._config.locale;
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
     * Load Google's apis and resolve the promise when ready.
     */
    Lava.prototype._loadGoogle = function (callback) {
        var $lava = this;
        var s = document.createElement('script');
        var deferred = Q.defer();

        s.type = 'text/javascript';
        s.async = true;
        s.src = this.urls.gstatic;
        s.onload = s.onreadystatechange = function (event) {
            event = event || window.event;

            if (event.type === "load" || (/loaded|complete/.test(this.readyState))) {
                this.onload = this.onreadystatechange = null;

                var packages = $lava._getPackages();
                var locale   = $lava._getLocale();

                console.log('google loaded');
                console.log(packages);

                google.charts.load('current', {
                    packages: packages,
                    language: locale
                });

                google.charts.setOnLoadCallback(deferred.resolve);
            }
        };

        document.head.appendChild(s);

        return deferred.promise;
    };

    /**
     * Initialize the Lava.js module by attaching the event listeners
     * and calling the charts' and dashboards' init methods
     *
     * @public
     */
    Lava.prototype.init = function () {
        console.log('lava.js init');

        var $lava = this;
        var readyCount = 0;

        this.on('ready', function (renderable) {
            console.log(renderable.uuid() + ' ready');

            readyCount++;

            if (readyCount == $lava._getRenderables().length) {
                console.log('loading google');

                $lava._loadGoogle().then(function() {
                    return $lava._mapRenderables(function (renderable) {
                        console.log('configuring ' + renderable.uuid());

                        return renderable.configure();
                    });
                }).then(function () {
                    return $lava._mapRenderables(function (renderable) {
                        console.log('rendering ' + renderable.uuid());

                        return renderable.render();
                    });
                }).then(function() {
                    console.log('lava.js ready');

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
        console.log('lava.js running');

        this._forEachRenderable(function (renderable) {
            console.log('init ' + renderable.uuid());

            renderable.init();
        });
    };

    return new Lava();
})();
||||||| merged common ancestors
=======
/* jshint undef: true, unused: true */
/* globals window, document, console, google, module, require */

/**
 * Lava module
 *
 * @module    lava/Lava
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2015, KHill Designs
 * @license   MIT
 */
module.exports = (function() {
    'use strict';

    var Q = require('q');
    var _ = require('lodash');
    var util = require('util');
    var EventEmitter = require('events');

    function Lava() {
        /**
         * Setting the debug flag
         *
         * @type {boolean}
         */
        this._debug = true;

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
         * Urls to Google's resources
         *
         * @type {{jsapi: string, gstatic: string}}
         */
        this.urls = {
            jsapi:   '//www.google.com/jsapi',
            gstatic: '//www.gstatic.com/charts/loader.js'
        };

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
         * Conditional debug logging method.
         *
         * @private
         */
        this._log = function (msg) {
            if (lava._debug) {
                console.log(msg);
            }
        };

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
     * Stores a renderable lava object within the module.
     *
     * @param {Chart|Dashboard} renderable
     */
    Lava.prototype.store = function (renderable) {
        if (renderable instanceof lava.Chart) {
            this.storeChart(renderable);
        }

        if (renderable instanceof lava.Dashboard) {
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
     * @param {Object} chart
     * @param {Function} callback
     * @return {Function}
     */
    Lava.prototype.event = function (event, chart, callback) {
        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        return callback(event, chart);
    };

    /**
     * Loads new data into the chart and redraws.
     *
     *
     * Used with an AJAX call to a PHP method returning DataTable->toJson(),
     * a chart can be dynamically update in page, without reloads.
     *
     * @public
     * @param {String} label
     * @param {String} json
     * @param {Function} callback
     */
    Lava.prototype.loadData = function (label, json, callback) {
        var callback = typeof callback !== 'undefined' ? callback : _.noop();

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this.getChart(label, function (chart) {
            if (typeof json.data != 'undefined') {
                chart.setData(json.data);
            } else {
                chart.setData(json);
            }

            if (typeof json.formats != 'undefined') {
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
     * @param {String} label
     * @param {String} json
     * @param {Function} callback
     */
    Lava.prototype.loadOptions = function (label, json, callback) {
        var callback = typeof callback !== 'undefined' ? callback : _.noop();

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
    Lava.prototype.redrawCharts = function () {
        _.debounce(_.bind(function () {
            this._forEachRenderable(function (renderable) {
                renderable.redraw();
            });
        }, this), 300);
    };

    /**
     * Create a new Chart.
     *
     * @public
     * @param  {String} type Type of chart to create
     * @param  {String} type Label for the chart
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
     * @param  {String}   label
     * @param  {Function} callback
     * @throws InvalidLabel
     * @throws InvalidCallback
     * @throws ChartNotFound
     */
    Lava.prototype.getChart = function (label, callback) {
        if (typeof label != 'string') {
            throw new this._errors.InvalidLabel(label);
        }

        if (typeof callback != 'function') {
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
     * @param  {String} label
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
     * @param  {String}   label    Dashboard label.
     * @param  {Function} callback Callback function
     * @throws InvalidLabel
     * @throws InvalidCallback
     * @throws DashboardNotFound
     */
    Lava.prototype.getDashboard = function (label, callback) {
        if (typeof label != 'string') {
            throw new this._errors.InvalidLabel(label);
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        var dash = _.find(this._dashboards, {label: label});

        if (dash instanceof lava.Dashboard === false) {
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
     * Load Google's apis and resolve the promise when ready.
     */
    Lava.prototype._loadGoogle = function (callback) {
        var $lava = this;
        var s = document.createElement('script');
        var deferred = Q.defer();

        s.type = 'text/javascript';
        s.async = true;
        s.src = this.urls.gstatic;
        s.onload = s.onreadystatechange = function (event) {
            event = event || window.event;

            if (event.type === "load" || (/loaded|complete/.test(this.readyState))) {
                this.onload = this.onreadystatechange = null;

                var packages = $lava._getPackages();

                $lava._log('google loaded');
                $lava._log(packages);

                google.charts.load('current', {
                    packages: packages
                });

                google.charts.setOnLoadCallback(deferred.resolve);
            }
        };

        document.head.appendChild(s);

        return deferred.promise;
    };

    /**
     * Initialize the Lava.js module by attaching the event listeners
     * and calling the charts' and dashboards' init methods
     *
     * @public
     */
    Lava.prototype.init = function () {
        this._log('lava.js init');

        var $lava = this;
        var readyCount = 0;

        this.on('ready', function (renderable) {
            $lava._log(renderable.uuid() + ' ready');

            readyCount++;

            if (readyCount == $lava._getRenderables().length) {
                $lava._log('loading google');

                $lava._loadGoogle().then(function() {
                    return $lava._mapRenderables(function (renderable) {
                        $lava._log('configuring ' + renderable.uuid());

                        return renderable.configure();
                    });
                }).then(function () {
                    return $lava._mapRenderables(function (renderable) {
                        $lava._log('rendering ' + renderable.uuid());

                        return renderable.render();
                    });
                }).then(function() {
                    $lava._log('lava.js ready');

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
        this._log('lava.js running');

        this._forEachRenderable(function (renderable) {
            lava._log('init ' + renderable.uuid());

            renderable.init();
        });
    };

    return new Lava();
})();
>>>>>>> f42cc1f1b7377d96af2a8b6708a3f5af466117a4
