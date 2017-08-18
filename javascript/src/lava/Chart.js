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
import Renderable from './Renderable';
import VisualizationProps from './VisualizationProps';
import { stringToFunction } from './Utils';

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
export default class Chart extends Renderable
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
        this.formats = json.formats;

        this.events    = typeof json.events === 'object' ? json.events : null;
        this.pngOutput = typeof json.pngOutput === 'undefined' ? false : Boolean(json.pngOutput);

        this.vizProps = new VisualizationProps(this.type);

        /**
         * Any dependency on window.google must be in the render scope.
         */
        this.render = () => {
            this.setData(json.datatable);

            console.log(this.vizProps.class);

            this.gchart = new google.visualization[this.vizProps.class](this.element);

            if (this.formats) {
                this.applyFormats();
            }

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
     * Get the type of package needed to render the chart.
     *
     * @return {string}
     */
    get package() {
        return this.vizProps.package;
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

    /**
     * Apply the formats to the DataTable
     *
     * @param {Array} formats
     * @public
     */
    applyFormats(formats) {
        if (! formats) {
            formats = this.formats;
        }

        for (let format of formats) {
            let formatter = new google.visualization[format.type](format.options);

            console.log(`[lava.js] Column index [${format.index}] formatted with:`, formatter);

            formatter.format(this.data, format.index);
        }
    }

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
}
