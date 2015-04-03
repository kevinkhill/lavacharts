<?php namespace Khill\Lavacharts\Laravel;

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

foreach ($charts as $chart) {
    $blade->extend(function ($view, $compiler) use ($chart) {
        $pattern = $compiler->createMatcher(strtolower($chart));
        $output  = '$1<?php echo Lava::render'.$chart.'$2; ?>';

        return preg_replace($pattern, $output, $view);
    });
}
