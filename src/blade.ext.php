<?php

Blade::extend(function($view, $compiler)
{
    $charts = array(
        'LineChart',
        'AreaChart',
        'ComboChart',
        'PieChart',
        'DonutChart',
        'GeoChart'
    );

    foreach ($charts as $chart)
    {
        $pattern = $compiler->createMatcher(strtolower($chart));
        $output  = '<?php echo Lava::render'.$chart.'$2; ?>';

        return preg_replace($pattern, $output, $view);
    }
});

//@render('LineChart', 'Stocks', 'sales_div')
//@linechart('Stocks', 'sales_div')
