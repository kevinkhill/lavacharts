<?php

require(realpath(__DIR__ . '/../../vendor/autoload.php'));

use Khill\Lavacharts\Charts\ChartFactory;

$lava = new \Khill\Lavacharts\Lavacharts;

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else {
    $chartType = trim($_SERVER["REQUEST_URI"], '/');

    if ($chartType !== "") {
        $width  = 600;
        $height = floor($width*(6/19));

        $title = 'My' . ((strpos($chartType, 'To') > 0) ? 'Dashboard' : $chartType);

        if (strpos($chartType, 'Chart') > 0) {
            require_once(__DIR__ . '/Charts/' . $chartType . '.php');
            /*$elemId = $lava->fetch($chartType, $title)->getElementIdStr();*/
        } else {
            require_once(__DIR__ . '/Dashboards/' . $chartType . '.php');
            /*$elemId = $lava->fetch('Dashboard', $title)->getElementIdStr();*/
        }

        $elemId = str_random(12);
    }
}
?>

<html>
    <head>
        <title>Lavacharts Examples</title>
        <style type="text/css">
            h1, h2 {font-family:Helvetica,Verdana,sans-serif;}
            #logo{text-align:center}
            #lavachart{width:99%}
            .float{float:left;padding:0 20px;margin-left:20px;}
            .grey{background-color:#f3f3f3;border:1px solid #666}
            ul{list-style-type:none;margin:0;padding:0}li{font:200 16px/1.5 Helvetica,Verdana,sans-serif;border-bottom:1px solid #ccc}li:last-child{border:none}li a{text-decoration:none;color:#000;display:block;width:200px;-webkit-transition:font-size .2s ease,background-color .2s ease;-moz-transition:font-size .2s ease,background-color .2s ease;-o-transition:font-size .2s ease,background-color .2s ease;-ms-transition:font-size .2s ease,background-color .2s ease;transition:font-size .2s ease,background-color .2s ease}li a:hover{font-size:18px;background:#f6f6f6}
        </style>
    </head>
    <body>
        <div id="logo">
            <img src="data:image/gif;base64,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"/>
        </div>
        <h1><?= $chartType ?></h1>
        <div class="render" id="<?= $elemId ?>">
            <? if ($chartType == 'Dashboard') { ?>
                <div id="chart-div-id"></div>
                <div id="control-div-id"></div>
            <? } ?>
        </div>
        <?php
        if ($chartType !== "") {
        ?>
            <? if (strpos($chartType, 'To') > 0) { ?>
            <div id="<?= $elemId ?>">
                <div id="chart1-div-id"></div>
                    <div id="chart2-div-id"></div>
                    <div id="control1-div-id"></div>
                    <div id="control2-div-id"></div>
                <? } ?>
            </div>
            <h2>Code</h2>
            <pre class="grey">
            <?php
                if (strpos($chartType, 'Chart') > 0) {
                    $file = file_get_contents(__DIR__ . '/Charts/' . $chartType . '.php');
                } else {
                    $file = file_get_contents(__DIR__ . '/Dashboards/' . $chartType . '.php');
                }

                echo ltrim($file, '<?php');
            ?>
            </pre>
<?php
            if (strpos($chartType, 'Chart') > 0) {
                echo $lava->render($chartType, $title, $elemId);
            } else {
                echo $lava->render('Dashboard', 'MyDashboard', $elemId);
            }
        } else {
?>
            <div class="float">
            <h1>Charts</h1>
            <ul>
<?php
            foreach (ChartFactory::getChartTypes() as $chartType) {
                echo sprintf('<li><a href="%1$s">%1$s</a></li>', $chartType);
            }
?>
            </ul>
            </div>

            <div class="float">
                <h1>Dashboards</h1>
                <ul>
                    <li><a href="OneToOne">One to One</a></li>
                    <li><a href="OneToMany">One to Many</a></li>
                    <li><a href="ManyToOne">Many to One</a></li>
                    <li><a href="ManyToMany">Many to Many</a></li>
                    <li><a href="OneToOneDateFilter">One to One [DateFilter]</a></li>
                </ul>
            </div>
<?php
        }
?>
    </body>
</html>

