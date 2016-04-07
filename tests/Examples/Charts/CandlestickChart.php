<?php
    use Khill\Lavacharts\DataTables\DataFactory;

    $data = DataFactory::arrayToDataTable([
        ['Mon', 20, 28, 38, 45],
        ['Tue', 31, 38, 55, 66],
        ['Wed', 50, 55, 77, 80],
        ['Thu', 77, 77, 66, 50],
        ['Fri', 68, 66, 22, 15]
        // Treat first row as data as well.
    ], true);

    $lava->CandlestickChart($title, $data, [
        'legend' => 'none',
        'height' => 400
    ]);
