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
import {getType} from "./Utils"
import {ElementIdNotFound} from "./Errors";
import getProperties from './VisualizationMap';

/**
 * Chart module
 *
 * @class     Chart
 * @module    lava/Chart
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
export default class Renderable
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
    constructor(json) {
        this.gchart    = null;
        this.type      = json.type;
        this.label     = json.label;
        this.options   = json.options;
        // this.packages  = json.packages;
        this.elementId = json.elementId;

        this.element = document.getElementById(this.elementId);

        if (! this.element) {
            throw new ElementIdNotFound(this.elementId);
        }
    }

    /**
     * The google.visualization class needed for rendering.
     *
     * @return {string}
     */
    get class()
    {
        return getProperties(this.type).class;
    }

    /**
     * The google.visualization class needed for rendering.
     *
     * @return {string}
     */
    get packages()
    {
        return getProperties(this.type).package;
    }

    /**
     * Unique identifier for the Chart.
     *
     * @return {string}
     */
    get uuid() {
        return this.type+'::'+this.label;
    }

    /**
     * Draws the chart with the preset data and options.
     *
     * @public
     */
    draw() {
        this.gchart.draw(this.data, this.options);
    }

    /**
     * Sets the data for the chart by creating a new DataTable
     *
     * @public
     * @external "google.visualization.DataTable"
     * @see   {@link https://developers.google.com/chart/interactive/docs/reference#DataTable|DataTable Class}
     * @param {object} payload Json representation of a DataTable
     */
    setData(payload) {
        // If the payload is from the php class JoinedDataTable->toJson(), then create
        // two new DataTables and join them with the defined options.
        if (getType(payload.data) === 'Array') {
            this.data = google.visualization.data.join(
                new google.visualization.DataTable(payload.data[0]),
                new google.visualization.DataTable(payload.data[1]),
                payload.keys,
                payload.joinMethod,
                payload.dt2Columns,
                payload.dt2Columns
            );

            return;
        }

        // Since Google compiles their classes, we can't use instanceof to check since
        // it is no longer called a "DataTable" (it's "gvjs_P" but that could change...)
        if (getType(payload.getTableProperties) === 'Function') {
            this.data = payload;

            return;
        }

        // If an Array is received, then attempt to use parse with arrayToDataTable.
        if (getType(payload) === 'Array') {
            this.data = google.visualization.arrayToDataTable(payload);

            return;
        }

        // If a php DataTable->toJson() payload is received, with formatted columns,
        // then payload.data will be defined, and used as the DataTable
        if (getType(payload.data) === 'Object') {
            payload = payload.data;

            // TODO: handle formats better...
        }

        // If we reach here, then it must be standard JSON for creating a DataTable.
        this.data = new google.visualization.DataTable(payload);
    }

    /**
     * Sets the options for the chart.
     *
     * @public
     * @param {object} options
     */
    setOptions(options) {
        this.options = options;
    }
}
