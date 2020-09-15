<?php
  require '../vendor/autoload.php';

  $lava = new Khill\Lavacharts\Lavacharts();

  $data = $lava->DataTable();
  $data->addColumns([
      ['string', 'name'],
      ['number', 'donuts'],
  ])->addRows([
      ['Jim', rand(0, 5)],
      ['Tom', rand(0, 5)],
      ['Mary', rand(0, 5)],
      ['Gary', rand(0, 5)],
      ['Larry', rand(0, 5)],
  ]);

  $chart = $lava->LineChart('Things', $data, [
      'elementId' => 'my-chart',
      'animation' => [
          'duration' => 300,
          'easing' => 'inandout',
      ],
  ]);
?>

<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lava Tests</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kognise/water.css@latest/dist/dark.min.css">
  <?php echo $lava->lavajs(); ?>
</head>

<body>
  <div id="my-chart"></div>

    <script>
    <?php echo $chart->toJavascript(); ?>
    lava.draw();
  </script>
</body>

</html>
