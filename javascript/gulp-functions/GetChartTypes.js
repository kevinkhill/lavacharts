import { map } from 'lodash';
import { cwd } from 'process';
import { resolve } from 'path';
import { sync as globSync } from 'glob';

export default function getChartTypes() {
    let chartTypes = globSync('*.php', {
        cwd: resolve(cwd(), '../src/Charts/'),
        nomount: true,
        ignore: [
            'Chart.php',
            'ChartBuilder.php',
            'ChartFactory.php',
        ]
    });

    return map(chartTypes, chartType => {
        return chartType.slice(0, -4);
    });
}
