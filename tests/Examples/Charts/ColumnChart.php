<?php
    $finances = $lava->DataTable();
    $finances->addColumn('date', 'Year')
             ->addColumn('number', 'Sales')
             ->addColumn('number', 'Expenses')
             ->setDateTimeFormat('Y')
             ->addRow(['2004', 1000, 400])
             ->addRow(['2005', 1170, 460])
             ->addRow(['2006', 660, 1120])
             ->addRow(['2007', 1030, 54]);

    $lava->ColumnChart($title, $finances, [
        'title' => 'Company Performance',
        'width' => $width,
        'height' => $height,
        'titleTextStyle' => [
            'color' => '#eb6b2c',
            'fontSize' => 14
        ]
    ]);
