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
import { Chart } from './Chart.es6';
import { Dashboard } from './Dashboard.es6';
import { noop } from './Utils.es6';
import { InvalidCallback, RenderableNotFound } from './Errors.es6'


/**
 * @property {string}             VERSION        Version of the module.
 * @property {Chart}              Chart          Chart class.
 * @property {Dashboard}          Dashboard      Dashboard class.
 * @property {object}             _errors
 * @property {string}             GOOGLE_LOADER_URL     Url to Google's static loader
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
         * @type {string}
         * @public
         */
        this.VERSION = '4.0.0';

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
        this._readyCallback = noop;
    }

    /**
     * Create a new Chart from a JSON definition.
     *
     * The JSON payload comes from the PHP Chart class.
     *
     * @public
     * @param  {object} json
     * @return {Chart}
     */
    createChart(json) {
        return new this.Chart(json);
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
        return new this.Dashboard(json);
    }

    /**
     * Runs the Lava.js module
     *
     * @public
     */
    run() {
        const $lava = this;

        console.log('[lava.js] Running...');
        console.log('[lava.js] Loading options:', this.options);

        $lava._loadGoogle().then(() => {
            console.log('[lava.js] Google is ready.');

            _forIn($lava._renderables, renderable => {
                console.log(`[lava.js] Rendering ${renderable.uuid()}`);

                renderable.render();
            });

            console.log('[lava.js] Executing lava.ready(callback)');
            $lava._readyCallback();

            console.log('[lava.js] Firing "ready" event.');
            $lava.emit('ready');
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
            this._packages = this._packages.merge(packages);
        }
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
        for (let renderable of this._renderables) {
            console.log(`[lava.js] Redrawing ${renderable.uuid()}`);

            const redraw = renderable.draw.bind(renderable);

            redraw();
        }
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
            }
        });
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
}
