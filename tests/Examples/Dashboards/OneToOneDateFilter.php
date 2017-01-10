<?php
    $data = $lava->DataTable([
        ['date', 'Dates'],
        ['number', 'Users Active']
    ], function () {
        $dates = [];

        for ($a=1; $a < 31; $a++) {
            $dates[] = ['2017-1-'.$a, rand(5000, 10000)];
        }

        return $dates;
    });

    $lineChart = $lava->LineChart('MyPie', $data, [
        'width' => $width,
        'height' => $height,
        'chartArea' => [
            'left' => 15,
            'top' => 15
        ],
        'pieSliceText' => 'value'
    ]);

    $filter = $lava->DateRangeFilter(0, [
        'ui' => [
            'label' => 'Date:',
            'labelStacking' => 'vertical'
        ]
    ]);

    $ctrlWrap  = $lava->ControlWrapper($filter, 'control1-div-id');
    $chartWrap = $lava->ChartWrapper($lineChart, 'chart1-div-id');

    $dash = $lava->Dashboard('MyDash')
                 ->bind($ctrlWrap, $chartWrap);
