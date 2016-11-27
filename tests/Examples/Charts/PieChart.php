<?php
    $reasons = $lava->DataTable();
    $reasons->addColumn('string', 'Reasons')
            ->addColumn('number', 'Percent')
            ->addRow(['Check Reviews', 5])
            ->addRow(['Watch Trailers', 2])
            ->addRow(['See Actors Other Work', 4])
            ->addRow(['Settle Argument', 89]);

    $lava->PieChart($title, $reasons, [
        'title' => 'Reasons I visit IMDB',
        'width' => $width,
        'height' => $height,
        'is3D' => true,
        'slices' => [
            ['offset' => 0.2],
            ['offset' => 0.25],
            ['offset' => 0.3]
        ]
    ]);



