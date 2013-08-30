<?php

View::share('lavaAssetPath', 'packages/khill/lavacharts/');

/**
 * LavaChart Home
 */
Route::get('/lavacharts', function()
{
    return View::make('lavacharts::home');
});

/**
 * LavaChart Examples
 */
Route::get('/lavacharts/examples', function()
{
    return View::make('lavacharts::examples');
});

/**
 * LavaChart Examples
 */
Route::get('/lavacharts/examples2', function()
{
    return View::make('lavacharts::examples2');
});

/**
 * Advanced Line Chart
 */
Route::get('/line/basic', function()
{
    $stocksTable = Lava::DataTable('Stocks');

    $stocksTable->addColumn('date', 'Date', 'date')
                ->addColumn('number', 'Projected', 'projected')
                ->addColumn('number', 'Closing', 'closing');

    for($a = 1; $a < 30; $a++)
    {
        $data = array(
            Lava::jsDate(2011, 5, $a), //Date
            rand(9500,10000),             //Line 1's data
            rand(9500,10000)              //Line 2's data
        );

        $stocksTable->addRow($data);
    }

    Lava::LineChart('Stocks')->title('Stock Market Trends');

    return View::make('lavacharts::line.basic');
});

/**
 * Advanced Line Chart
 */
Route::get('/line/advanced', function()
{
    $timesTable = Lava::DataTable('Times');

    $timesTable->addColumn('date', 'Dates', 'dates')
               ->addColumn('number', 'Estimated Time', 'schedule')
               ->addColumn('number', 'Actual Time', 'run');

    for($a = 1; $a < 30; $a++)
    {
        $data = array(
            Lava::jsDate(2013, 8, $a), //Date object
            rand(5,30),                //Line 1's data
            rand(5,30),                //Line 2's data
        );

        $timesTable->addRow($data);
    }

    //Either Chain functions together and assign to variables
    $legendStyle = Lava::textStyle()->color('#F3BB00')
                                    ->fontName('Arial')
                                    ->fontSize(20);

    $legend = Lava::legend()->position('bottom')
                            ->alignment('start')
                            ->textStyle($legendStyle);


    //Or pass in arrays with set options into the function's constructor
    $tooltip = Lava::tooltip(array(
                    'showColorCode' => TRUE,
                    'textStyle' => Lava::textStyle(array(
                        'color' => '#C0C0B0',
                        'fontName' => 'Courier New',
                        'fontSize' => 10
                    ))
                ));


    $config = array(
        'backgroundColor' => Lava::backgroundColor(array(
            'stroke' => '#113bc9',
            'strokeWidth' => 4,
            'fill' => '#ffd'
        )),
        'chartArea' => Lava::chartArea(array(
            'left' => 100,
            'top' => 75,
            'width' => '85%',
            'height' => '55%'
        )),
        'titleTextStyle' => Lava::textStyle(array(
            'color' => '#FF0A04',
            'fontName' => 'Georgia',
            'fontSize' => 18
        )),
        'legend' => $legend,
        'tooltip' => $tooltip,
        'title' => 'Times for Deliveries',
        'titlePosition' => 'out',
        'curveType' => 'function',
        'width' => 1000,
        'height' => 450,
        'pointSize' => 3,
        'lineWidth' => 1,
        'colors' => array('#4F9CBB', 'green'),
        'hAxis' => Lava::hAxis(array(
            'baselineColor' => '#fc32b0',
            'gridlines' => array(
                'color' => '#43fc72',
                'count' => 6
            ),
            'minorGridlines' => array(
                'color' => '#b3c8d1',
                'count' => 3
            ),
            'textPosition' => 'out',
            'textStyle' => Lava::textStyle(array(
                'color' => '#C42B5F',
                'fontName' => 'Tahoma',
                'fontSize' => 10
            )),
            'slantedText' => TRUE,
            'slantedTextAngle' => 30,
            'title' => 'Delivery Dates',
            'titleTextStyle' => Lava::textStyle(array(
                'color' => '#BB33CC',
                'fontName' => 'Impact',
                'fontSize' => 14
            )),
            'maxAlternation' => 6,
            'maxTextLines' => 2
        )),
        'vAxis' => Lava::vAxis(array(
            'baseline' => 5,
            'baselineColor' => '#CF3BBB',
            'format' => '## Min.',
            'textPosition' => 'out',
            'textStyle' => Lava::textStyle(array(
                'color' => '#DDAA88',
                'fontName' => 'Arial Bold',
                'fontSize' => 10
            )),
            'title' => 'Delivery Time',
            'titleTextStyle' => Lava::textStyle(array(
                'color' => '#5C6DAB',
                'fontName' => 'Verdana',
                'fontSize' => 14
            )),
        ))
    );

    Lava::LineChart('Times')->setConfig($config);

    return View::make('lavacharts::line.advanced');
});