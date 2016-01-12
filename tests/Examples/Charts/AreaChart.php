<?php
    $data = $lava->DataTable();
    $data->addDateColumn('Date')
         ->addNumberColumn('Thing 1')
         ->addNumberColumn('Thing 2');

    for ($a=1;$a<30;$a++) {
        $data->addRow(['2016-1-'.$a, rand(100,200), rand(100,200)]);
    }

    $lava->AreaChart('Things!', $data);
?>

<html>
    <head></head>
    <body>
        <div id="chart"></div>
        <?= $lava->render('AreaChart', 'Things!', 'chart'); ?>
    </body>
</html>
