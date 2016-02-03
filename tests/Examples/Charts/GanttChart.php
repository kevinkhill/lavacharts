<?php
    use \Khill\Lavacharts\DataTables\DataFactory;

    $data = DataFactory::DataTable([
        ['string', 'Task ID'],
        ['string', 'Task Name'],
        ['date', 'Start Date'],
        ['date', 'End Date'],
        ['number', 'Duration'],
        ['number', 'Percent Complete'],
        ['string', 'Dependencies'],
    ],[
        ['Research', 'Find sources',
         '2015-1-1', '2015-1-5', null,  100,  null],
        ['Write', 'Write paper',
         null, '2015-1-9', '2015-1-12', 25, 'Research,Outline'],
        ['Cite', 'Create bibliography',
         null, '2015-1-7', '2015-1-8', 20, 'Research'],
        ['Complete', 'Hand in paper',
         null, '2015-1-1', '2015-1-2', 0, 'Cite,Write'],
        ['Outline', 'Outline paper',
         null, '2015-1-6', '2015-1-7', 100, 'Research']
    ]);

    $lava->GanttChart($title, $data, ['height' => 275]);
