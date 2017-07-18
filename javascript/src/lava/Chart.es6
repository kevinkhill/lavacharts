
/**
 * Chart module
 *
 * @class     Chart
 * @module    lava/Chart
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
import _ from 'lodash';
import { Renderable } from './Renderable.es6';
import { stringToFunction } from './Utils.es6';


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

        this.gchart    = null;
        this.class     = json.class     || null;
        this.formats   = json.formats   || null;

        this.events    = typeof json.events === 'object' ? new Map(json.events) : null;
        this.pngOutput = typeof json.pngOutput === 'undefined' ? false : Boolean(json.pngOutput);

        /**
         * Any dependency on window.google must be in the render scope.
         */
        this.render = function() {
            this.setData(json.datatable);

            let chartClass = stringToFunction(this.class, window);

            this.gchart = new chartClass(this.element);

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
        this.gchart.draw(this.data, this.options);
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
            img.src = this.gchart.getImageURI();

        this.element.innerHTML = '';
        this.element.appendChild(img);
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
             * to the callback as an argument.
             */
            google.visualization.events.addListener($chart.gchart, event, function() {
                let callback = _.bind(context[func], $chart.gchart);

                callback($chart.data);
            });
        });
    }
}
