<?php
    $data = $lava->DataTable();
    $data->addStringColumn('Name');
    $data->addNumberColumn('Donuts Eaten');
    $data->addRows([
        ['Michael',   5],
        ['Elisa',     7],
        ['Robert',    3],
        ['John',      2],
        ['Jessica',   6],
        ['Aaron',     1],
        ['Margareth', 8]
    ]);

    $pieChart = $lava->PieChart('Stuff', $data, [
        'width' => 300,
        'height' => 200,
        'chartArea' => [
            'left' => 15,
            'top' => 15
        ],
        'pieSliceText' => 'value'
    ]);

    $filter  = $lava->NumberRangeFilter(1, [
        'ui'=> [
            'label' => 'Donuts Eaten:',
            'labelStacking' => 'vertical'
        ]
    ]);
    $control = $lava->ControlWrapper($filter, 'control-div-id');
    $chart   = $lava->ChartWrapper($pieChart, 'chart-div-id');
    $dash    = $lava->Dashboard('Donuts')
                    ->bind($control, $chart);
?>

<html>
    <head></head>
    <body>
        <div id="dashboard-div-id">
            <div id="chart-div-id"></div>
            <div id="control-div-id"></div>
        </div>
        <?= $lava->render('Dashboard', 'Donuts', 'dashboard-div-id'); ?>
    </body>
</html>
