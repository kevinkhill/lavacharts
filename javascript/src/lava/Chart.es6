/**
 * Chart module
 *
 * @class     Chart
 * @module    lava/Chart
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
import _forIn from 'lodash/forIn';
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
 * @property {Object}   events    - Events and callbacks to apply to the chart.
 * @property {Array}    formats   - Formatters to apply to the chart data.
 * @property {Function} render    - Renders the chart.
 * @property {Function} uuid      - Creates identification string for the chart.
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

        this.type    = json.type;
        this.class   = json.class;
        this.formats = json.formats;

        this.events    = typeof json.events === 'object' ? json.events : null;
        this.pngOutput = typeof json.pngOutput === 'undefined' ? false : Boolean(json.pngOutput);

        /**
         * Any dependency on window.google must be in the render scope.
         */
        this.render = () => {
            this.setData(json.datatable);

            let ChartClass = stringToFunction(this.class, window);

            this.gchart = new ChartClass(this.element);

            if (this.formats) {
                this._applyFormats();
            }

            if (this.events) {
                this._attachEvents();
                // TODO: Idea... forward events to be listenable by the user, instead of having the user define them as a string callback.
                // lava.get('MyCoolChart').on('ready', function(data) {
                //     console.log(this);  // gChart
                // });
            }

            this.draw();

            if (this.pngOutput) {
                this.drawPng();
            }
        };
    }

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
    }

    // /**
    //  * Formats columns of the DataTable.
    //  *
    //  * @public
    //  * @param {Array.<Object>} formatArr Array of format definitions
    //  */
    // applyFormats(formatArr) {
    //     for(let a=0; a < formatArr.length; a++) {
    //         let formatJson = formatArr[a];
    //         let formatter = new google.visualization[formatJson.type](formatJson.config);
    //
    //         formatter.format(this.data, formatJson.index);
    //     }
    // }

    /**
     * Attach the defined chart event handlers.
     *
     * @private
     */
    _attachEvents() {
        let $chart = this;

        _forIn(this.events, function (callback, event) {
            let context = window;
            let func = callback;

            if (typeof callback === 'object') {
                context = context[callback[0]];
                func = callback[1];
            }

            console.log(`[lava.js] The "${$chart.uuid()}::${event}" event will be handled by "${func}" in the context`, context);

            /**
             * Set the context of "this" within the user provided callback to the
             * chart that fired the event while providing the datatable of the chart
             * to the callback as an argument.
             */
            google.visualization.events.addListener($chart.gchart, event, function() {
                const callback = context[func].bind($chart.gchart);

                callback($chart.data);
            });
        });
    }

    /**
     * Apply the formats to the DataTable
     *
     * @param {Array} formats
     * @private
     */
    _applyFormats() {
        for (let format of this.formats) {
            let formatter = new google.visualization[format.type](format.options);

            console.log(`[lava.js] Created new format for column index [${format.index}]`, formatter);

            formatter.format(this.data, format.index);
        }
    }
}
