<?php

require('../../vendor/autoload.php');

use \Khill\Lavacharts\Charts\ChartFactory;

echo json_encode(ChartFactory::$CHART_TYPES);
