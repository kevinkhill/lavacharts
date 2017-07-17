/**
 * Chart module
 *
 * @class     Chart
 * @module    lava/Chart
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */

import { Renderable } from './Renderable';
import { stringToFunction } from './Utils';
import _ from 'lodash';

/**
 * Chart class used for storing all the needed configuration for rendering.
 *
 * @typedef {Function}  Chart
 * @property {string}   label     - Label for the chart.
 * @property {string}   type      - Type of chart.
 * @property {Object}   element   - Html element in which to render the chart.
 * @property {Object}   chart     - Google chart object.
 * @property {string}   package   - Type of Google chart package to load.
 * @property {boolean}  pngOutput - Should the chart be displayed as a PNG.
 * @property {Object}   data      - Datatable for the chart.
 * @property {Object}   options   - Configuration options for the chart.
 * @property {Array}    formats   - Formatters to apply to the chart data.
 * @property {Object}   promises  - Promises used in the rendering chain.
 * @property {Function} init      - Initializes the chart.
 * @property {Function} configure - Configures the chart.
 * @property {Function} render    - Renders the chart.
 * @property {Function} uuid      - Creates identification string for the chart.
 * @property {Object}   _errors   - Collection of errors to be thrown.
 */
export class Chart extends Renderable
{
    /**
     * Chart Class
     *
     * This is the javascript version of a lavachart with methods for interacting with
     * the google chart and the PHP lavachart output.
     *
     * @param {object} json
     * @constructor
     */
    constructor (json) {
        super(json);

        console.log('[DEBUG] JSON payload received', json);

        this.element   = null;
        this.elementId = null;
        this.chart     = null;
        this.class     = json.class     || '';
        this.options   = json.options   || [];
        this.events    = json.events    || null;
        this.formats   = json.formats   || null;
        this.pngOutput = json.pngOutput || false;

        this.setElement(json.elementId);

        /**
         * Any dependency on window.google must be in the render scope.
         */
        this.render = function() {
            this.setData(json.datatable);

            let chartClass = stringToFunction(this.class, window);

            this.chart = new chartClass(this.element);

            // <formats>

            if (this.events) {
                this._attachEvents();
            }

            this.draw();

            if (this.pngOutput) {
                this.drawPng();
            }
        };
    }

    /**
     * Redraws the chart.
     *
     * @public
     */
    draw() {
        this.chart.draw(this.data, this.options);
    };

    /**
     * Draws the chart as a PNG instead of the standard SVG
     *
     * @public
     * @external "chart.getImageURI"
     * @see {@link https://developers.google.com/chart/interactive/docs/printing|Printing PNG Charts}
     */
    drawPng() {
        let img = document.createElement('img');
            img.src = this.chart.getImageURI();

        this.element.innerHTML = '';
        this.element.appendChild(img);
    };

    /**
     * Sets whether the chart is to be rendered as PNG or SVG
     *
     * @public
     * @param {string|int} png
     */
    setPngOutput(png) {
        this.pngOutput = Boolean(typeof png === 'undefined' ? false : png);
    };

    /**
     * Formats columns of the DataTable.
     *
     * @public
     * @param {Array.<Object>} formatArr Array of format definitions
     */
    applyFormats(formatArr) {
        for(let a=0; a < formatArr.length; a++) {
            let formatJson = formatArr[a];
            let formatter = new google.visualization[formatJson.type](formatJson.config);

            formatter.format(this.data, formatJson.index);
        }
    };

    /**
     * Attach the defined chart event handlers.
     *
     * @private
     */
    _attachEvents() {
        let $chart = this;

        _.forIn(this.events, function (callback, event) {
            let context = window;
            let func = callback;

            if (typeof callback === 'object') {
                context = context[callback[0]];
                func = callback[1];
            }

            console.log(`[lava.js] The "${$chart.uuid()}::${event}" event will be handled by "${func}" in the context of "${context}"`);

            /**
             * Set the context of "this" within the user provided callback to the
             * chart that fired the event while providing the datatable of the chart
             * to the callback.
             */
            google.visualization.events.addListener($chart.chart, event, function() {
                let callback = _.bind(context[func], $chart.chart);

                callback($chart.data);
            });
        });
    }
}
