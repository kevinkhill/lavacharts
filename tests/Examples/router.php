<?php

require('../../vendor/autoload.php');

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else {
    $chart = trim($_SERVER["REQUEST_URI"], '/');

    $lava = new \Khill\Lavacharts\Lavacharts;

    require_once(__DIR__ . '\\Charts\\' . $chart . '.php');
}
