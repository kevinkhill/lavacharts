<?php
    $data = $lava->DataTable();
    $data->addColumns([
        ['date',   'Date'],
        ['number', 'Kepler-22b mission'],
        ['string', 'Kepler title'],
        ['string', 'Kepler text'],
        ['number', 'Gliese 163 mission'],
        ['string', 'Gliese title'],
        ['string', 'Gliese text']
    ])->addRows([
          ['2314-2-15', 12400, null, null,
                                  10645, null, null],
          ['2314-2-16', 24045, 'Lalibertines', 'First encounter',
                                  12374, null, null],
          ['2314-2-17', 35022, 'Lalibertines', 'They are very tall',
                                  15766, 'Gallantors', 'First Encounter'],
          ['2314-2-18', 12284, 'Lalibertines', 'Attack on our crew!',
                                  34334, 'Gallantors', 'Statement of shared principles'],
          ['2314-2-19', 8476, 'Lalibertines', 'Heavy casualties',
                                  66467, 'Gallantors', 'Mysteries revealed'],
          ['2314-2-20', 0, 'Lalibertines', 'All crew lost',
                                  79463, 'Gallantors', 'Omniscience achieved']
        ]);

    $lava->AnnotationChart($title, $data, [
        'displayAnnotations' => true
    ]);
