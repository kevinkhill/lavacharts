<?php

require('../../vendor/autoload.php');

use \Khill\Lavacharts\Charts\ChartFactory;

$lava = new \Khill\Lavacharts\Lavacharts;

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else {
    $chart = trim($_SERVER["REQUEST_URI"], '/');

    $lava = new \Khill\Lavacharts\Lavacharts;

    if ($chart !== "") {
        $width  = 600;
        $height = floor($width*(6/19));

        $title = 'My'.$chart;
        $id = strtolower($chart);

        require_once(__DIR__ . '/Charts/' . $chart . '.php');
    }
}
?>

<html>
    <head>
        <title>Lavacharts Test</title>
        <style type="text/css">
            #logo{text-align:center}
            pre{background-color:#f3f3f3;border:1px solid #666;}
        </style>
    </head>
    <body>
        <div class="render" id="<?= $id ?>">
            <? if ($chart == 'Dashboard') { ?>
                <div id="chart-div-id"></div>
                <div id="control-div-id"></div>
            <? } ?>
        </div>
<?php
        if ($chart !== "") {
?>
            <h1><?= $chart ?></h1>
            <div id="lavachart">
                <? if ($chart == 'Dashboard') { ?>
                    <div id="chart-div-id"></div>
                    <div id="control-div-id"></div>
                <? } ?>
            </div>
            <h1>Code</h1>
            <pre>
            <?php
                $file = file_get_contents(__DIR__ . '/Charts/' . $chart . '.php');
                echo ltrim($file, '<?php');
            ?>
            </pre>
<?php
            echo $lava->render($chart, $title, 'lavachart');
        } else {
            echo '<h1>Supported Charts</h1>';
            echo '<ul>';
            foreach (ChartFactory::chartTypes() as $chart) {
                echo sprintf('<li><a href="%1$s">%1$s</a></li>', $chart);
            }
            echo '</ul>';
            echo '<p><a href="Dashboard">Dashboard</a></p>';
        }
?>
    </body>
</html>

