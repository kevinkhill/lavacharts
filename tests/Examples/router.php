<?php

require('../../vendor/autoload.php');

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else {
    $chart = trim($_SERVER["REQUEST_URI"], '/');

    $lava = new \Khill\Lavacharts\Lavacharts;

    $width = 900;
    $height = 400;

    $title = 'My'.$chart;
    $id = strtolower($chart);

    require_once(__DIR__ . '/Charts/' . $chart . '.php');
}
?>

<html>
    <head></head>
    <body>
        <div class="render" id="<?= $id ?>">
            <? if ($chart == 'Dashboard') { ?>
                <div id="chart-div-id"></div>
                <div id="control-div-id"></div>
            <? } ?>
        </div>

        <?= $lava->render($chart, $title, $id); ?>
    </body>
</html>

