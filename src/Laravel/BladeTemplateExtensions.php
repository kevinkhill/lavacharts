<?php

namespace Khill\Lavacharts\Laravel;

use Illuminate\Support\Facades\App;

$app   = App::getFacadeApplication();
$blade = $app['view']->getEngineResolver()->resolve('blade')->getCompiler();

$charts = array(
    'AreaChart',
    'BarChart',
    'CalendarChart',
    'ColumnChart',
    'ComboChart',
    'DonutChart',
    'GaugeChart',
    'GeoChart',
    'LineChart',
    'PieChart'
);

/**
 * If the directive method exists, we're using Laravel 5
 */
if (method_exists($blade, 'directive')) {
    foreach ($charts as $chart) {
        $blade->directive(strtolower($chart), function($expression) use ($chart) {
            return '<?php echo Lava::render'. $chart . $expression . '; ?>';
        });
    }
} else {
    foreach ($charts as $chart) {
        $blade->extend(function ($view, $compiler) use ($chart) {
            $pattern = $compiler->createMatcher(strtolower($chart));
            $output  = '$1<?php echo Lava::render'.$chart.'$2; ?>';
            return preg_replace($pattern, $output, $view);
        });
    }
}
