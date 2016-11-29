<?php
    $votes = $lava->DataTable();
    $votes->addStringColumn('Food')
          ->addNumberColumn('Votes')
          ->addRow(['Tacos', rand(1000,5000)])
          ->addRow(['Salad', rand(1000,5000)])
          ->addRow(['Pizza', rand(1000,5000)])
          ->addRow(['Apples', rand(1000,5000)])
          ->addRow(['Fish', rand(1000,5000)]);

    $lava->BarChart($title, $votes, [
        'width' => $width,
        'height' => $height,
        'legend' => [
            'position' => 'top'
        ]
    ]);
