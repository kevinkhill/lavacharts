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

    $pieChart = $lava->PieChart('MyPie', $lava->DataTable(), [
        'width' => $width,
        'height' => $height,
        'chartArea' => [
            'left' => 15,
            'top' => 15
        ],
        'pieSliceText' => 'value'
    ]);

    $barChart = $lava->BarChart('MyBar', $lava->DataTable());

    $filter = $lava->NumberRangeFilter(1, [
        'ui' => [
            'label' => 'Donuts Eaten:',
            'labelStacking' => 'vertical'
        ]
    ]);

    $ctrlWrap = $lava->ControlWrapper($filter, 'control1-div-id');
    $pieWrap  = $lava->ChartWrapper($pieChart, 'chart1-div-id');
    $barWrap  = $lava->ChartWrapper($barChart, 'chart2-div-id');

    $dash = $lava->Dashboard('MyDashboard', $data, 'OneToManyDash')
                 ->bind($ctrlWrap, [$pieWrap, $barWrap]);
