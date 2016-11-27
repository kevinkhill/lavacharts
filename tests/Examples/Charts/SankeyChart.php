<?php
    $data = $lava->DataTable();
    $data->addColumn('string', 'From')
         ->addColumn('string', 'To')
         ->addColumn('number', 'Weight')
         ->addRows([
             [ 'A', 'X', 5 ],
             [ 'A', 'Y', 7 ],
             [ 'A', 'Z', 6 ],
             [ 'B', 'X', 2 ],
             [ 'B', 'Y', 9 ],
             [ 'B', 'Z', 4 ]
         ]);

    $colors = ['#a6cee3', '#b2df8a', '#fb9a99', '#fdbf6f', '#cab2d6', '#ffff99', '#1f78b4', '#33a02c'];

    $lava->SankeyChart($title, $data, [
        'width' => $width,
        'height' => $height,
        'legend' => [
            'position' => 'none'
        ],
        'sankey' => [
            'node' => [
                'colors' => $colors
            ],
            'link' => [
                'colorMode' => 'gradient',
                'colors' => $colors
            ]
        ]
    ]);
