import glob from 'glob';
import { pullAll, map } from 'lodash';
import { cwd } from 'process';
import { resolve } from 'path';

export default function getChartTypes(callback) {
    glob('*.php', {
        cwd: resolve(cwd(), '../src/Charts/'),
        nomount: true
    }, (err, chartTypes) => {
        pullAll(chartTypes, [
            'Chart.php',
            'ChartBuilder.php',
            'ChartFactory.php',
        ]);

        callback(map(chartTypes, chartType => {
            return chartType.slice(0, -4);
        }));
    });
}
