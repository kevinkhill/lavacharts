/* jshint undef: true, unused: true */
/* globals window, document, console, google, module, require */

import EventEmitter from 'events';
import { Chart } from './Chart';
import { Dashboard } from './Dashboard';
import _ from 'lodash';

const util = require('util');
const VERSION = require('../../package.json').version;

/**
 * lava.js module
 *
 * @module    lava/Lava
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   http://opensource.org/licenses/MIT MIT
 *
 * @property {string}             VERSION        Version of the module.
 * @property {Chart}              Chart          Chart class.
 * @property {Dashboard}          Dashboard      Dashboard class.
 * @property {object}             _errors
 * @property {string}             GSTATIC_URL     Url to Google's static loader
 * @property {object}             options        Options for the module
 * @property {function}           _readyCallback
 * @property {Array.<string>}     _packages
 * @property {Array.<Renderable>} _renderables
 */
export class LavaJs extends EventEmitter
{
    constructor() {
        super();

        /**
         * Version of the Lava.js module.
         *
         * @public
         * @type {string}
         */
        this.VERSION = VERSION;

        /**
         * Version of the Google charts API to load.
         *
         * @public
         * @type {string}
         */
        this.API_VERSION = 'current';

        /**
         * Urls to Google's static loader
         *
         * @public
         * @type {string}
         */
        this.GSTATIC_URL = 'https://www.gstatic.com/charts/loader.js';

        /**
         * The event that is fired after the google object is loaded.
         *
         * @public
         * @type {string}
         */
        this.GOOGLE_LOADED_EVENT = 'google:loaded';

        /**
         * Defining the Chart class within the module.
         *
         * @type {Chart}
         */
        this.Chart = Chart;
        /**
         * Defining the Dashboard class within the module.
         *
         * @type {Dashboard}
         */

        // this.Dashboard = Dashboard;

        /**
         * JSON object of config items.
         *
         * @type {Object}
         * @private
         */
        this.options = OPTIONS_JSON;

        /**
         * Array of visualization packages for charts and dashboards.
         *
         * @type {Array.<string>}
         * @private
         */
        this._packages = [];

        /**
         * Array of charts and dashboards stored in the module.
         *
         * @type {Array.<Renderable>}
         * @private
         */
        this._renderables = [];

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
    }

    /**
     * Runs the Lava.js module
     *
     * @public
     */
    run() {
        console.log('[lava.js] Running...');
        console.log('[lava.js] Loading options:', this.options);

        const $lava = this;

        $lava.loadGoogle().then(function() {
            console.log('[lava.js] Google is ready.');

            _.forIn($lava._renderables, function (renderable) {
                console.log(`[lava.js] Rendering ${renderable.uuid()}`);

                renderable.render();
            });
        });
    };

    /**
     * Create a new Chart from the PHP Chart::toJson() method.
     *
     * @public
     * @param  {object} json JSON data for creating a new chart.
     * @return {Chart}
     */
    createChart(json) {
        return new this.Chart(json);
    };

    /**
     * Create a new Dashboard with a given label.
     *
     * @public
     * @param  {string} label
     * @return {Dashboard}
     */
    createDashboard(label) {
        return new this.Dashboard(label);
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
    event(event, lavachart, callback) {
        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        return callback(event).bind(lavachart.chart);
    };

    /**
     * Stores a renderable lava object within the module.
     *
     * @param {Renderable} renderable
     */
    store(renderable) {
        console.log('[lava.js] Storing', renderable.uuid());

        this._renderables[renderable.label] = renderable;
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
    ready(callback) {
        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this._readyCallback = callback;
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
    loadData(label, json, callback) {
        if (typeof callback === 'undefined') {
            callback = _.noop;
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this.get(label, function (chart) {
            if (typeof json.data !== 'undefined') {
                chart.setData(json.data);
            } else {
                chart.setData(json);
            }

            if (typeof json.formats !== 'undefined') {
                chart.applyFormats(json.formats);
            }

            chart.draw();

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
    loadOptions(label, json, callback) {
        if (typeof callback === 'undefined') {
            callback = callback || _.noop;
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        this.get(label, function (chart) {
            chart.setOptions(json);
            chart.draw();

            callback(chart);
        });
    };

    /**
     * Redraws all of the registered charts on screen.
     *
     * This method is attached to the window resize event with debouncing
     * to make the charts responsive to the browser resizing.
     */
    redrawAll() {
        this.forEachRenderable(function (renderable) {
            console.log('[lava.js] ' + renderable.uuid() + ' -> redrawing');

            const redraw = _.bind(renderable.draw, renderable);

            redraw();
        });
    };

    /**
     * Returns an array with the charts and dashboards.
     *
     * @public
     * @return {Array}
     */
    getAll(callback) {
        callback(this._renderables);
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
    get(label, callback) {
        if (typeof label !== 'string') {
            throw new this._errors.InvalidLabel(label);
        }

        if (typeof callback !== 'function') {
            throw new this._errors.InvalidCallback(callback);
        }

        let renderable = this._renderables[label];

        if (! renderable) {
            throw new this._errors.ChartNotFound(label);
        }

        callback(renderable);
    };

    /**
     * Adds to the list of packages that Google needs to load.
     *
     * Can accept a string name for one package or an array of
     * names.
     *
     * @public
     * @param {object|string} packages
     * @return {Array}
     */
    addPackages(packages) {
        if (typeof packages === 'string') {
            this._packages.push(packages);
        }

        if (typeof packages === 'object') {
            this._packages = _.merge(this._packages, packages);
        }
    }

    /**
     * Check if Google's Static Loader is in page.
     *
     * @private
     * @returns {boolean}
     */
    _googleIsLoaded() {
        const scripts = document.getElementsByTagName('script');

        let loaded = false;

        for (let i = scripts.length; i--;) {
            if (scripts[i].src === this.GSTATIC_URL) {
                loaded = true;
            }
        }

        return loaded;
    };

    /**
     * Load the Google Static Loader and resolve the promise when ready.
     *
     * @private
     * @returns
     */
    loadGoogle() {
        const $lava = this;

        return new Promise((resolve, reject) => {
            console.log('[lava.js] Resolving Google...');

            if (this._googleIsLoaded()) {
                console.log('[lava.js] Static loader found, initializing window.google');

                $lava._googleChartLoader(resolve);
            } else {
                console.log('[lava.js] Static loader not found, appending to head');

                $lava._addGoogleScriptToHead(resolve);
            }
        });
    };

    /**
     * Create a new script tag for the Google Static Loader.
     *
     * @private
     * @param {Promise.resolve} resolve
     * @returns {Element}
     */
    _addGoogleScriptToHead(resolve) {
        let $lava = this;
        let script = document.createElement('script');

        script.type = 'text/javascript';
        script.async = true;
        script.src = this.GSTATIC_URL;
        script.onload = script.onreadystatechange = function (event) {
            event = event || window.event;

            if (event.type === 'load' || (/loaded|complete/.test(this.readyState))) {
                this.onload = this.onreadystatechange = null;

                $lava._googleChartLoader(resolve);
            }
        };

        document.head.appendChild(script);
    };

    /**
     * Runs the Google chart loader and resolves the promise.
     *
     * @private
     * @param {Promise.resolve} resolve
     */
    _googleChartLoader(resolve) {
        let config = {
            packages: this._packages,
            language: this.options.locale
        };

        if (this.options.maps_api_key !== '') {
            config.mapsApiKey = this.options.maps_api_key;
        }

        console.log('[lava.js] Loading Google with config:', config);

        google.charts.load(this.API_VERSION, config);

        google.charts.setOnLoadCallback(resolve);
    };
}
