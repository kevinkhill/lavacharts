<?php

namespace Khill\Lavacharts\Laravel;

use Illuminate\Support\Facades\App;

/**
 * Blade Template Extensions
 *
 * These extend blade templates to allow for a shorter syntax for rendering charts.
 * Instead of using {{ Lava::render('LineChart', 'MyChart', 'div-id') }}
 * you can use the chart type (or dashboard) as an @ directive.
 * The above example would turn into @linechart('MyChart', 'div-id')
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Laravel
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

$app   = App::getFacadeApplication();
$blade = $app['view']->getEngineResolver()->resolve('blade')->getCompiler();

$charts = [
    'Dashboard',
    'AreaChart',
    'BarChart',
    'CalendarChart',
    'ColumnChart',
    'ComboChart',
    'DonutChart',
    'GaugeChart',
    'GeoChart',
    'LineChart',
    'PieChart',
    'ScatterChart',
    'TableChart'
];


foreach ($charts as $chart) {

    if (method_exists($blade, 'directive')) {
// Laravel 5
        $blade->directive(strtolower($chart), function ($expression) use ($chart) {
            $expression = ltrim($expression, '(');
            $expression = rtrim($expression, ')');

            return "<?php echo \Lava::render('$chart', $expression); ?>";
        });

    } else {

// Laravel 4
        $blade->extend(
            function ($view, $compiler) use ($chart) {
                $pattern = $compiler->createMatcher(strtolower($chart));
                $output = '$1<?php echo \Lava::render' . $chart . '$2; ?>';

                return preg_replace($pattern, $output, $view);
            }
        );

    }
}
