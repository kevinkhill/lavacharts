<?php
    $popularity = $lava->DataTable();
    $popularity->addStringColumn('Country')
               ->addNumberColumn('Popularity')
               ->addRow(['Germany', 200])
               ->addRow(['United States', 300])
               ->addRow(['Brazil', 400])
               ->addRow(['Canada', 500])
               ->addRow(['France', 600])
               ->addRow(['RU', 700]);

    $lava->GeoChart($title, $popularity, [
        'width' => $width,
        'height' => $height
    ]);
