/* jshint node:true */

const _ = require('lodash');
const glob = require('glob');

export function getChartTypes(callback) {
    glob('*.php', {
        cwd: '../src/Charts/',
        nomount: true
    }, (err, chartTypes) => {
        _.pullAll(chartTypes, [
            'Chart.php',
            'ChartBuilder.php',
            'ChartFactory.php',
        ]);

        callback(_.map(chartTypes, chartType => {
            return chartType.slice(0, -4);
        }));
    });
}
