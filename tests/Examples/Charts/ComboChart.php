<?php
    $finances = $lava->DataTable();
    $finances->addDateColumn('Year')
             ->addNumberColumn('Sales')
             ->addNumberColumn('Expenses')
             ->addNumberColumn('Net Worth')
             ->addRow(['2009-1-1', 1100, 490, 1324])
             ->addRow(['2010-1-1', 1000, 400, 1524])
             ->addRow(['2011-1-1', 1400, 450, 1351])
             ->addRow(['2012-1-1', 1250, 600, 1243])
             ->addRow(['2013-1-1', 1100, 550, 1462]);

    $lava->ComboChart($title, $finances, [
        'elementId' => 'chart',
        'title' => 'Company Performance',
        'width' => $width,
        'height' => $height,
        'titleTextStyle' => [
            'color' => 'rgb(123, 65, 89)',
            'fontSize' => 16,
            'bold' => true
        ],
        'legend' => [
            'position' => 'in'
        ],
        'seriesType' => 'bars',
        'series' => [
            2 => [
                'type' => 'line'
            ]
        ]
    ]);
