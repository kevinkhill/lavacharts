<?php
    $chart = $_GET['chart'];

    require('../../vendor/autoload.php');

    $lava = new \Khill\Lavacharts\Lavacharts;

    require_once(__DIR__ . '\\' . $chart . '.php');
