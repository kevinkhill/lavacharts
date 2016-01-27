<?php
    $reasons = $lava->DataTable();
    $reasons->addColumn('string', 'Reasons')
            ->addColumn('number', 'Percent')
            ->addRow(['Check Reviews', 5])
            ->addRow(['Watch Trailers', 2])
            ->addRow(['See Actors Other Work', 4])
            ->addRow(['Settle Argument', 89]);

    $lava->DonutChart('IMDB', $reasons, [
        'title'=>'Reasons I visit IMDB',
        'width' => $width,
        'height' => $height,
    ]);
?>

<html>
    <head></head>
    <body>
        <div class="render" id="chart"></div>
        <?= $lava->render('DonutChart', 'IMDB', 'chart'); ?>
    </body>
</html>

