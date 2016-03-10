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

    $pieChart = $lava->PieChart('MyPie', $data, [
        'width' => $width,
        'height' => $height,
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

    $controlWrap = $lava->ControlWrapper($filter, 'control-div-id');
    $chartWrap   = $lava->ChartWrapper($pieChart, 'chart-div-id');

    $dash = $lava->Dashboard($title)
                 ->bind($controlWrap, $chartWrap);
