/* jshint browser:true */
/* globals google:true */

/**
 * lava.js module
 *
 * @module    lava/Lava
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   http://opensource.org/licenses/MIT MIT
 */
import _forIn from 'lodash/forIn';
import EventEmitter from 'events';
import Chart from './Chart';
import Dashboard from './Dashboard';
import defaultOptions from './Options';
import { noop, addEvent } from './Utils';
import { InvalidCallback, RenderableNotFound } from './Errors'


/**
 * @property {string}             VERSION
 * @property {string}             GOOGLE_API_VERSION
 * @property {string}             GOOGLE_LOADER_URL
 * @property {Chart}              Chart
 * @property {Dashboard}          Dashboard
 * @property {object}             options
 * @property {function}           _readyCallback
 * @property {Array.<string>}     _packages
 * @property {Array.<Renderable>} _renderables
 */
export default class LavaJs extends EventEmitter
{
    constructor(newOptions) {
        super();

        /**
         * Version of the Lava.js module.
         *
         * @type {string}
         * @public
         */
        this.VERSION = '__VERSION__';

        /**
         * Version of the Google charts API to load.
         *
         * @type {string}
         * @public
         */
        this.GOOGLE_API_VERSION = 'current';

        /**
         * Urls to Google's static loader
         *
         * @type {string}
         * @public
         */
        this.GOOGLE_LOADER_URL = 'https://www.gstatic.com/charts/loader.js';

        /**
         * Storing the Chart module within Lava.js
         *
         * @type {Chart}
         * @public
         */
        this.Chart = Chart;

        /**
         * Storing the Dashboard module within Lava.js
         *
         * @type {Dashboard}
         * @public
         */
        this.Dashboard = Dashboard;

        /**
         * JSON object of config items.
         *
         * @type {Object}
         * @public
         */
        this.options = newOptions || defaultOptions;

        /**
         * Reference to the google.visualization object.
         *
         * @type {google.visualization}
         */
        this.visualization = null;

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
        this._readyCallback = noop;
    }

    /**
     * Create a new Chart from a JSON payload.
     *
     * The JSON payload comes from the PHP Chart class.
     *
     * @public
     * @param  {object} json
     * @return {Renderable}
     */
    createChart(json) {
        console.log('Creating Chart', json);

        this._addPackages(json.packages); // TODO: move this into the store method?

        return new this.Chart(json);
    }

    /**
     * Create and store a new Chart from a JSON payload.
     *
     * @public
     * @see createChart
     * @param {object} json
     */
    addNewChart(json) { //TODO: rename to storeNewChart(json) ?
        this.store(this.createChart(json));
    }

    /**
     * Create a new Dashboard with a given label.
     *
     * The JSON payload comes from the PHP Dashboard class.
     *
     * @public
     * @param  {object} json
     * @return {Dashboard}
     */
    createDashboard(json) {
        console.log('Creating Dashboard', json);

        this._addPackages(json.packages);

        return new this.Dashboard(json);
    }

    /**
     * Create and store a new Dashboard from a JSON payload.
     *
     * The JSON payload comes from the PHP Dashboard class.
     *
     * @public
     * @see createDashboard
     * @param  {object} json
     * @return {Dashboard}
     */
    addNewDashboard(json) { //TODO: rename to storeNewDashboard(json) ?
        this.store(this.createDashboard(json));
    }

    /**
     * Public method for initializing google on the page.
     *
     * @public
     */
    init() {
        return this._loadGoogle().then(() => {
            this.visualization = google.visualization;
        });
    }

    /**
     * Runs the Lava.js module
     *
     * @public
     */
    run() {
        // const $lava = this;

        console.log('[lava.js] Running...');
        console.log('[lava.js] Loading options:', this.options);

        this._attachRedrawHandler();

        this.init().then(() => {
            console.log('[lava.js] Google is ready.');

            _forIn(this._renderables, renderable => {
                console.log(`[lava.js] Rendering ${renderable.uuid()}`);

                renderable.render();
            });

            console.log('[lava.js] Firing "ready" event.');
            this.emit('ready');

            console.log('[lava.js] Executing lava.ready(callback)');
            this._readyCallback();
        });
    }

    /**
     * Stores a renderable lava object within the module.
     *
     * @param {Renderable} renderable
     */
    store(renderable) {
        console.log(`[lava.js] Storing ${renderable.uuid()}`);

        this._renderables[renderable.label] = renderable;
    }

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
     * @throws RenderableNotFound
     */
    get(label, callback) {
        if (typeof callback !== 'function') {
            throw new InvalidCallback(callback);
        }

        let renderable = this._renderables[label];

        if (! renderable) {
            throw new RenderableNotFound(label);
        }

        callback(renderable);
    }

    /**
     * Assigns a callback for when the charts are ready to be interacted with.
     *
     * This is used to wrap calls to lava.loadData() or lava.loadOptions()
     * to protect against accessing charts that aren't loaded yet
     *
     * @public
     * @param {function} callback
     */
    ready(callback) {
        if (typeof callback !== 'function') {
            throw new InvalidCallback(callback);
        }

        this._readyCallback = callback;
    }

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
            callback = noop;
        }

        if (typeof callback !== 'function') {
            throw new InvalidCallback(callback);
        }

        this.get(label, function (chart) {
            chart.setData(json);

            if (typeof json.formats !== 'undefined') {
                chart.applyFormats(json.formats);
            }

            chart.draw();

            callback(chart);
        });
    }

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
            callback = callback || noop;
        }

        if (typeof callback !== 'function') {
            throw new InvalidCallback(callback);
        }

        this.get(label, function (chart) {
            chart.setOptions(json);
            chart.draw();

            callback(chart);
        });
    }

    /**
     * Redraws all of the registered charts on screen.
     *
     * This method is attached to the window resize event with debouncing
     * to make the charts responsive to the browser resizing.
     */
    redrawAll() {
        if (this._renderables.length === 0) {
            console.log(`[lava.js] Nothing to redraw.`);

            return false;
        } else {
            console.log(`[lava.js] Redrawing ${this._renderables.length} renderables.`);
        }

        for (let renderable of this._renderables) {
            console.log(`[lava.js] Redrawing ${renderable.uuid()}`);

            let redraw = renderable.draw.bind(renderable);

            redraw();
        }

        return true;
    }

    /**
     * Aliasing google.visualization.arrayToDataTable to lava.arrayToDataTable
     *
     * @public
     * @param {Array} arr
     * @return {google.visualization.DataTable}
     */
    arrayToDataTable(arr) {
        return this.visualization.arrayToDataTable(arr);
    }

    /**
     * Adds to the list of packages that Google needs to load.
     *
     * @private
     * @param {Array} packages
     * @return {Array}
     */
    _addPackages(packages) {
        this._packages = this._packages.concat(packages);
    }

    /**
     * Attach a listener to the window resize event for redrawing the charts.
     *
     * @private
     */
    _attachRedrawHandler() {
        if (this.options.responsive === true) {
            let debounced = null;

            addEvent(window, 'resize', () => {
                // let redraw = this.redrawAll().bind(this);

                clearTimeout(debounced);

                debounced = setTimeout(() => {
                    console.log('[lava.js] Window re-sized, redrawing...');

                    // redraw();
                    this.redrawAll()
                }, this.options.debounce_timeout);
            });
        }
    }

    /**
     * Load the Google Static Loader and resolve the promise when ready.
     *
     * @private
     */
    _loadGoogle() {
        const $lava = this;

        return new Promise(resolve => {
            console.log('[lava.js] Resolving Google...');

            if (this._googleIsLoaded()) {
                console.log('[lava.js] Static loader found, initializing window.google');

                $lava._googleChartLoader(resolve);
            } else {
                console.log('[lava.js] Static loader not found, appending to head');

                $lava._addGoogleScriptToHead(resolve);
                // This will call $lava._googleChartLoader(resolve);
            }
        });
    }

    /**
     * Check if Google's Static Loader is in page.
     *
     * @private
     * @returns {boolean}
     */
    _googleIsLoaded() {
        const scripts = document.getElementsByTagName('script');

        for (let script of scripts) {
            if (script.src === this.GOOGLE_LOADER_URL) {
                return true;
            }
        }
    }

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

        google.charts.load(this.GOOGLE_API_VERSION, config);

        google.charts.setOnLoadCallback(resolve);
    }

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
        script.src = this.GOOGLE_LOADER_URL;
        script.onload = script.onreadystatechange = function (event) {
            event = event || window.event;

            if (event.type === 'load' || (/loaded|complete/.test(this.readyState))) {
                this.onload = this.onreadystatechange = null;

                $lava._googleChartLoader(resolve);
            }
        };

        document.head.appendChild(script);
    }
}
