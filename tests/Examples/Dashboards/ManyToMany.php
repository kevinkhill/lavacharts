<?php
    $data = $lava->DataTable();
    $data->addStringColumn('Name');
    $data->addNumberColumn('Donuts Eaten');
    $data->addNumberColumn('Age');
    $data->addRows([
        ['Michael',   5, 23],
        ['Elisa',     7, 28],
        ['Robert',    3, 25],
        ['John',      2, 22],
        ['Jessica',   6, 26],
        ['Aaron',     1, 28],
        ['Margareth', 8, 20]
    ]);

    $columnChart = $lava->ColumnChart($title, null, [
        'width' => $width,
        'height' => $height,
        'isStacked' => true,
        'chartArea' => [
            'left' => 30,
            'top' => 15
        ]
    ]);

    $areaChart = $lava->AreaChart($title, null, [
        'width' => $width,
        'height' => $height,
        'chartArea' => [
            'left' => 30,
            'top' => 15
        ]
    ]);

    $donutFilter = $lava->NumberRangeFilter(1, [
        'ui' => [
            'label' => 'Donuts Eaten:',
            'labelStacking' => 'vertical'
        ]
    ]);

    $ageFilter = $lava->NumberRangeFilter('Age', [
        'ui' => [
            'label' => 'Age:',
            'labelStacking' => 'vertical'
        ]
    ]);

    $donutFilterWrap = $lava->ControlWrapper($donutFilter, 'control1-div-id');
    $ageFilterWrap   = $lava->ControlWrapper($ageFilter, 'control2-div-id');
    $columnChartWrap = $lava->ChartWrapper($columnChart, 'chart1-div-id');
    $areaChartWrap   = $lava->ChartWrapper($areaChart, 'chart2-div-id');

    $lava->Dashboard('MyDashboard', $data, 'ManyToManyDash')
         ->bind([$donutFilterWrap, $ageFilterWrap], [$columnChartWrap, $areaChartWrap]);
