<?php
    $data = $lava->DataTable();
    $data->addDateColumn('Date')
         ->addNumberColumn('Thing 1')
         ->addNumberColumn('Thing 2');

    for ($a=1;$a<30;$a++) {
        $data->addRow(['2017-1-'.$a, rand(100,200), rand(100,200)]);
    }

    $lava->AreaChart($title, $data, [
        'width' => $width,
        'height' => $height,
        'legend' => 'none',
        'chartArea'=> [
            'left' => 50,
            'width' => '90%'
        ],
    ]);
