<?php
  $votes = $lava->DataTable();
  $votes->addStringColumn('Food')
        ->addNumberColumn('Votes')
        ->addRow(array('Tacos', rand(1000,5000)))
        ->addRow(array('Salad', rand(1000,5000)))
        ->addRow(array('Pizza', rand(1000,5000)))
        ->addRow(array('Apples', rand(1000,5000)))
        ->addRow(array('Fish', rand(1000,5000)));

  $lava->BarChart('Votes', $votes);
?>

<html>
    <head></head>
    <body>
        <div id="chart"></div>
        <?= $lava->render('BarChart', 'Votes', 'chart'); ?>
    </body>
</html>
