<?php
    $data = $lava->DataTable([
        ['string', 'Name'],
        ['number', 'Donuts Eaten']
    ],[
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

    $filter = $lava->NumberRangeFilter(1, [
        'ui' => [
            'label' => 'Donuts Eaten:',
            'labelStacking' => 'vertical'
        ]
    ]);

    $ctrlWrap  = $lava->ControlWrapper($filter, 'control1-div-id');
    $chartWrap = $lava->ChartWrapper($pieChart, 'chart1-div-id');

    $dash = $lava->Dashboard('MyDashboard', $data, 'OneToOneDash')
                 ->bind($ctrlWrap, $chartWrap);
