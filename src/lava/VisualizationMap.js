/**
 * Visualization Property Map
 *
 * This module provides the needed properties for rendering charts indexed
 * by the chart type.
 *
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @license   MIT
 */
const PROPERTY_MAP = {
    "AnnotationChart": {
        "class": "AnnotationChart",
        "package": "annotationchart",
        "version": 1
    },
    "AreaChart": {
        "class": "AreaChart",
        "package": "corechart",
        "version": 1
    },
    "BarChart": {
        "class": "BarChart",
        "package": "corechart",
        "version": 1
    },
    "BubbleChart": {
        "class": "BubbleChart",
        "package": "corechart",
        "version": 1
    },
    "CalendarChart": {
        "class": "Calendar",
        "package": "calendar",
        "version": 1.1
    },
    "CandlestickChart": {
        "class": "CandlestickChart",
        "package": "corechart",
        "version": 1
    },
    "ColumnChart": {
        "class": "ColumnChart",
        "package": "corechart",
        "version": 1
    },
    "ComboChart": {
        "class": "ComboChart",
        "package": "corechart",
        "version": 1
    },
    "DonutChart": {
        "class": "PieChart",
        "package": "corechart",
        "version": 1
    },
    "GanttChart": {
        "class": "Gantt",
        "package": "gantt",
        "version": 1
    },
    "GaugeChart": {
        "class": "Gauge",
        "package": "gauge",
        "version": 1
    },
    "GeoChart": {
        "class": "GeoChart",
        "package": "geochart",
        "version": 1
    },
    "HistogramChart": {
        "class": "Histogram",
        "package": "corechart",
        "version": 1
    },
    "LineChart": {
        "class": "LineChart",
        "package": "corechart",
        "version": 1
    },
    "PieChart": {
        "class": "PieChart",
        "package": "corechart",
        "version": 1
    },
    "SankeyChart": {
        "class": "Sankey",
        "package": "sankey",
        "version": 1
    },
    "ScatterChart": {
        "class": "ScatterChart",
        "package": "corechart",
        "version": 1
    },
    "SteppedAreaChart": {
        "class": "SteppedAreaChart",
        "package": "corechart",
        "version": 1
    },
    "TableChart": {
        "class": "Table",
        "package": "table",
        "version": 1
    },
    "TimelineChart": {
        "class": "Timeline",
        "package": "timeline",
        "version": 1
    },
    "TreeMapChart": {
        "class": "TreeMap",
        "package": "treemap",
        "version": 1
    },
    "WordTreeChart": {
        "class": "WordTree",
        "package": "wordtree",
        "version": 1
    }
};

export default function getProperties(chartType) {
    return PROPERTY_MAP[chartType];
}
