<?php namespace Khill\Lavacharts\Laravel;

$charts = array(
    'LineChart',
    'AreaChart',
    'ColumnChart',
    'ComboChart',
    'PieChart',
    'DonutChart',
    'GeoChart'
);

foreach ($charts as $chart)
{
    Blade::extend(function($view, $compiler) use ($chart) {
        $pattern = $compiler->createMatcher(strtolower($chart));
        $output  = '<?php echo Lava::render'.$chart.'$2; ?>';

        return preg_replace($pattern, $output, $view);
    });
}

// OLDEST: Lava::LineChart('Stocks')->outputInto('sales_div')
// OLDER:  Lava::render('LineChart', 'Stocks', 'sales_div')
// NEW:    @linechart('Stocks', 'sales_div')
