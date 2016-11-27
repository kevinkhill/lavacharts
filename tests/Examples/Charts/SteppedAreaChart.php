<?php
    use Khill\Lavacharts\DataTables\DataFactory;

    $data = DataFactory::DataTable([
        ['string', 'Director (Year)'],
        ['number', 'Rotten Tomatoes'],
        ['number', 'IMDB']
    ], [
        ['Alfred Hitchcock (1935)', 8.4, 7.9],
        ['Ralph Thomas (1959)',     6.9, 6.5],
        ['Don Sharp (1978)',        6.5, 6.4],
        ['James Hawes (2008)',      4.4, 6.2]
    ]);

    $lava->SteppedAreaChart($title, $data, [
        'title' => 'The decline of \'The 39 Steps\'',
        'width' => $width,
        'height' => $height,
        'vAxis' => [
            'title' => 'Accumulated Rating'
        ],
        'isStacked' => true,
        'legend' => 'none'
    ]);
