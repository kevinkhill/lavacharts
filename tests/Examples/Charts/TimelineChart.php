<?php
    use Carbon\Carbon;

    $data = $lava->DataTable();
    $data->addStringColumn('Room');
    $data->addStringColumn('Name');
    $data->addDateColumn('Start');
    $data->addDateColumn('End');
    $data->addRows([
        [ 'Magnolia Room', 'Beginning JavaScript',       Carbon::parse('12:00pm'), Carbon::parse('1:30pm') ],
        [ 'Magnolia Room', 'Intermediate JavaScript',    Carbon::parse('2:00pm'),  Carbon::parse('3:30pm') ],
        [ 'Magnolia Room', 'Advanced JavaScript',        Carbon::parse('4:00pm'),  Carbon::parse('5:30pm') ],
        [ 'Willow Room',   'Beginning Google Charts',    Carbon::parse('12:30pm'), Carbon::parse('2:30pm') ],
        [ 'Willow Room',   'Intermediate Google Charts', Carbon::parse('3:00pm'), Carbon::parse('4:30pm') ],
        [ 'Willow Room',   'Advanced Google Charts',     Carbon::parse('5:00pm'), Carbon::parse('7:00pm') ]
    ]);


    $lava->TimelineChart($title, $data, [
        'title' => 'Classes',
        'width' => $width,
        'height' => $height,
        'timeline' => [
            'colorByRowLabel' => true
        ]
    ]);
