/**
 * VisualizationProps class
 *
 * This module provides the needed properties for rendering charts retrieved
 * by the chart type.
 *
 * @class     VisualizationProps
 * @module    lava/VisualizationProps
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
export default class VisualizationProps
{
    /**
     * Build a new VisualizationProps class for the given chart type.
     *
     * @param {string} chartType
     */
    constructor(chartType) {
        this.chartType = chartType;

        /**
         * Map of chart types to their visualization package.
         */
        this.CHART_TYPE_PACKAGE_MAP = {
            AnnotationChart : 'annotationchart',
            AreaChart       : 'corechart',
            BarChart        : 'corechart',
            BubbleChart     : 'corechart',
            CalendarChart   : 'calendar',
            CandlestickChart: 'corechart',
            ColumnChart     : 'corechart',
            ComboChart      : 'corechart',
            DonutChart      : 'corechart',
            GanttChart      : 'gantt',
            GaugeChart      : 'gauge',
            GeoChart        : 'geochart',
            HistogramChart  : 'corechart',
            LineChart       : 'corechart',
            PieChart        : 'corechart',
            SankeyChart     : 'sankey',
            ScatterChart    : 'corechart',
            SteppedAreaChart: 'corechart',
            TableChart      : 'table',
            TimelineChart   : 'timeline',
            TreeMapChart    : 'treemap',
            WordTreeChart   : 'wordtree'
        };

        /**
         * Map of chart types to their visualization class name.
         */
        this.CHART_TYPE_CLASS_MAP = {
            AnnotationChart : 'AnnotationChart',
            AreaChart       : 'AreaChart',
            BarChart        : 'BarChart',
            BubbleChart     : 'BubbleChart',
            CalendarChart   : 'Calendar',
            CandlestickChart: 'CandlestickChart',
            ColumnChart     : 'ColumnChart',
            ComboChart      : 'ComboChart',
            DonutChart      : 'PieChart',
            GanttChart      : 'Gantt',
            GaugeChart      : 'Gauge',
            GeoChart        : 'GeoChart',
            HistogramChart  : 'Histogram',
            LineChart       : 'LineChart',
            PieChart        : 'PieChart',
            SankeyChart     : 'Sankey',
            ScatterChart    : 'ScatterChart',
            SteppedAreaChart: 'SteppedAreaChart',
            TableChart      : 'Table',
            TimelineChart   : 'Timeline',
            TreeMapChart    : 'TreeMap',
            WordTreeChart   : 'WordTree'
        };

        /**
         * Map of chart types to their versions.
         */
        this.CHART_TYPE_VERSION_MAP = {
            AnnotationChart : 1,
            AreaChart       : 1,
            BarChart        : 1,
            BubbleChart     : 1,
            CalendarChart   : 1.1,
            CandlestickChart: 1,
            ColumnChart     : 1,
            ComboChart      : 1,
            DonutChart      : 1,
            GanttChart      : 1,
            GaugeChart      : 1,
            GeoChart        : 1,
            HistogramChart  : 1,
            LineChart       : 1,
            PieChart        : 1,
            SankeyChart     : 1,
            ScatterChart    : 1,
            SteppedAreaChart: 1,
            TableChart      : 1,
            TimelineChart   : 1,
            TreeMapChart    : 1,
            WordTreeChart   : 1
        };
    }

    /**
     * Return the visualization package for the chart type
     *
     * @return {string}
     */
    getPackage() {
        return this.CHART_TYPE_PACKAGE_MAP[this.chartType];
    }

    /**
     * Return the visualization class for the chart type
     *
     * @return {string}
     */
    getClass() {
        return this.CHART_TYPE_CLASS_MAP[this.chartType];
    }

    /**
     * Return the visualization version for the chart type
     *
     * @return {number}
     */
    getVersion() {
        return this.CHART_TYPE_VERSION_MAP[this.chartType];
    }
}
