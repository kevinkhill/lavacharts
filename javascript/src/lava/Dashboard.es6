/**
 * Dashboard module
 *
 * @class     Dashboard
 * @module    lava/Dashboard
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
import { Renderable } from './Renderable.es6';
import { stringToFunction } from './Utils.es6';

/**
 * Dashboard class
 *
 * @typedef {Function}  Dashboard
 * @property {string}   label     - Label for the Dashboard.
 * @property {string}   type      - Type of visualization (Dashboard).
 * @property {Object}   element   - Html element in which to render the chart.
 * @property {string}   package   - Type of visualization package to load.
 * @property {Object}   data      - Datatable for the Dashboard.
 * @property {Object}   options   - Configuration options.
 * @property {Array}    bindings  - Chart and Control bindings.
 * @property {Function} render    - Renders the Dashboard.
 * @property {Function} uuid      - Unique identifier for the Dashboard.
 */
export class Dashboard extends Renderable
{
    constructor(json) {
        super(json);

        this.type     = 'Dashboard';
        this.bindings = json.bindings;

        /**
         * Any dependency on window.google must be in the render scope.
         */
        this.render = () => {
            this.setData(json.datatable);

            this.gchart = new google.visualization.Dashboard(this.element);

            this._attachBindings();

            if (this.events) {
                this._attachEvents();
            }

            this.draw();
        };
    }

    // @TODO: this needs to be modified for the other types of bindings.

    /**
     * Process and attach the bindings to the dashboard.
     *
     * @private
     */
    _attachBindings() {
        for (let binding of this.bindings) {
            // let [controlWrapper, chartWrapper] = binding;

            console.log('binding', binding);
            console.log('controlWrapper', binding.controlWrappers);
            console.log('chartWrapper', binding.chartWrappers);

            this.gchart.bind(
                new google.visualization.ControlWrapper(
                    binding.controlWrappers[0]
                ),
                new google.visualization.ChartWrapper(
                    binding.chartWrappers[0]
                )
            );
        }
    }
}
