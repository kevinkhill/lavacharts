<?php
    $temps = $lava->DataTable();
    $temps->addStringColumn('Type')
          ->addNumberColumn('Value')
          ->addRow(['CPU', rand(0,100)])
          ->addRow(['Case', rand(0,100)])
          ->addRow(['Graphics', rand(0,100)]);

    $lava->GaugeChart('Temps', $temps, [
        'width' => 400,
        'greenFrom' => 0,
        'greenTo' => 69,
        'yellowFrom' => 70,
        'yellowTo' => 89,
        'redFrom' => 90,
        'redTo' => 100,
        'majorTicks' => [
            'Safe',
            'Critical'
        ]
    ]);
?>

<html>
    <head></head>
    <body>
        <div id="chart"></div>
        <?= $lava->render('GaugeChart', 'Temps', 'chart'); ?>
    </body>
</html>
