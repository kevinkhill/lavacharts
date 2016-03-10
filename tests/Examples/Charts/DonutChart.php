<?php
    $reasons = $lava->DataTable();
    $reasons->addColumn('string', 'Reasons')
            ->addColumn('number', 'Percent')
            ->addRow(['Check Reviews', 5])
            ->addRow(['Watch Trailers', 2])
            ->addRow(['See Actors Other Work', 4])
            ->addRow(['Settle Argument', 89]);

    $lava->DonutChart($title, $reasons, [
        'title'=>'Reasons I visit IMDB',
        'width' => $width,
        'height' => $height,
    ]);
