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
    $blade->directive(strtolower($chart), function($expression) use ($chart) {
        return '<?php echo Lava::render'.$chart . $expression . '; ?>';
    });
}
