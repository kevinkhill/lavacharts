<?php
    use Khill\Lavacharts\DataTables\DataFactory;

    $daysToMilliseconds = function ($days) {
        return $days * 24 * 60 * 60 * 1000;
    };

    $data = DataFactory::DataTable([
        ['string', 'Task ID'],
        ['string', 'Task Name'],
        ['date', 'Start Date'],
        ['date', 'End Date'],
        ['number', 'Duration'],
        ['number', 'Percent Complete'],
        ['string', 'Dependencies'],
    ], [
        ['Research', 'Find sources',
         '2015-1-1', '2015-1-5', null,  100,  null],
        ['Write', 'Write paper',
         null, '2015-1-9', $daysToMilliseconds(3), 25, 'Research,Outline'],
        ['Cite', 'Create bibliography',
         null, '2015-1-7', $daysToMilliseconds(1), 20, 'Research'],
        ['Complete', 'Hand in paper',
         null, '2015-1-10', $daysToMilliseconds(1), 0, 'Cite,Write'],
        ['Outline', 'Outline paper',
         null, '2015-1-6', $daysToMilliseconds(1), 100, 'Research']
    ]);

    $lava->GanttChart($title, $data, [
        'height' => 275
    ]);
