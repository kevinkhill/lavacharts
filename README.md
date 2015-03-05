# Lavacharts [![Total Downloads](https://poser.pugx.org/khill/lavacharts/downloads.svg)](https://packagist.org/packages/khill/lavacharts) [![License](https://img.shields.io/packagist/l/khill/lavacharts.svg?style=plastic)](https://packagist.org/packages/khill/lavacharts) [![PayPayl](https://img.shields.io/badge/paypal-donate-yellow.svg?style=plastic)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FLP6MYY3PYSFQ)

Lavacharts is a graphing / chart library for PHP5.3+ that wraps the Google Chart API


Stable:
[![Current Release](https://img.shields.io/github/release/kevinkhill/lavacharts.svg?style=plastic)](https://github.com/kevinkhill/lavacharts/releases)
[![Build Status](https://img.shields.io/travis/kevinkhill/lavacharts/2.1.svg?style=plastic)](https://travis-ci.org/kevinkhill/lavacharts)
[![Coverage Status](https://img.shields.io/coveralls/kevinkhill/lavacharts/2.1.svg?style=plastic)](https://coveralls.io/r/kevinkhill/lavacharts?branch=2.1)


Dev:
![Masater Branch](https://img.shields.io/badge/branch-dev--master-brightgreen.svg?style=plastic)
[![Build Status](https://img.shields.io/travis/kevinkhill/lavacharts/master.svg?style=plastic)](https://travis-ci.org/kevinkhill/lavacharts)
[![Coverage Status](https://img.shields.io/coveralls/kevinkhill/lavacharts/master.svg?style=plastic)](https://coveralls.io/r/kevinkhill/lavacharts?branch=master)


Version 2 Features
==================
- Blade template extensions for laravel
- A new "lava" javascript api
- Javascript event integration
- Datatable addColumn aliases
- Datatable column formatters
- Carbon support for date columns
- Now supporting 9 Charts!
  - Area
  - Calendar
  - Column
  - Combo
  - Donut
  - Gauge
  - Geo
  - Line
  - Pie

###Complete documentation with examples, and the api can be found at [Lavacharts.com](http://lavacharts.com/)


Installing
----------
In your project's main ```composer.json``` file, add this line to the requirements:

  ```
  "khill/lavacharts": "2.1.*"
  ```

Run Composer to install Lavacharts:

  ```
  composer update
  ```

For Laravel
-----------
Register Lavacharts in your app by adding this line to the end of the providers array in ```app/config/app.php```:

  ```
  'providers' => array(
      ...

      "Khill\Lavacharts\Laravel\LavachartsServiceProvider"
  ),
  ```

  Don't worry about the ```Lava``` alias, the service provider registers it automatically.

Usage
-----
The creation of charts is separated into two parts:
First, within a route or controller, you define the chart, the data table, and the customization of the output.

Second, within a view, you use one line and the library will output all the necessary javascript code for you.

Basic Example
-------------
Here is an example of the simplest chart you can create: A line chart with one dataset and a title, no configuration.

Controller
==========
```
    $stocksTable = $lava->DataTable();  // Lava::DataTable() if using Laravel

    $stocksTable->addDateColumn('Day of Month')
                ->addNumberColumn('Projected')
                ->addNumberColumn('Official');

    // Random Data For Example
    for ($a = 1; $a < 30; $a++)
    {
        $rowData = array(
          "2014-8-$a", rand(800,1000), rand(800,1000)
        );

        $stocksTable->addRow($rowData);
    }
```

Arrays work for datatables as well...
```
  $stocksTable->addColumns(array(
    array('date', 'Day of Month'),
    array('number', 'Projected'),
    array('number', 'Official')
  ));
```

...and for setting chart options!
```
  $lineChart = $lava->LineChart('Stocks')
                    ->setOptions(array(
                        'datatable' => $stocksTable,
                        'title' => 'Stock Market Trends'
                      ));
```

View
====
If you are using Laravel and the Blade templating engine, there are some nifty extensions thrown in for a cleaner view

  ```
  @linechart('Stocks', 'stocks-div');
  // Behind the scenes this just calls Lava::renderLineChart('Stocks', 'stocks-div')
  ```

Or you can use the new render method, passing in the chart type, label, and element id.

  ```
  echo Lava::render('LineChart', 'Stocks', 'stocks-div');
  ```

This is all assuming you already have a div in your page with the id "stocks-div":
```<div id="stocks-div"></div>```

If you don't have a div ready to accept the charts, add one more parameter to ```@linechart()``` or ```render()``` and it will be created for you.

Add ```true``` to for the library to create a plain div, or an array with keys ```width & height```

Example:
```
  @linechart('Stocks', 'stocks-div', true)
  // Or
  echo Lava::render('LineChart', 'Stocks', 'stocks-div', array('width'=>1024, 'height'=>768));
```

Charts can be rendered from the ```$lava``` master object you created, as shown above, or you can pass the chart object to your view, and call the ```render()``` method with the element id of your div. This will bypass needing to specify the type and title of the chart.

Notice
======
If you are using Lavacharts with Composer and not in Laravel, that's fine, just make sure to:
```require 'vendor/autoload.php';``` within you project.

Create an instance of Lavacharts: ```$lava = new Khill\Lavacharts\Lavacharts;```

Replace all of the ```Lava::``` aliases in the examples, by chaining from the Lavacharts object you created.

Use ```$dt = $lava->DataTable();``` instead of ```$dt = Lava::DataTable();```


Changelog
---------
 - 2.1
   - Calendar Chart support

 - 2.0.5
   - Updated Carbon
   - Laravel 5 compatability

 - 2.0.4
   - Multiple chart bug fixes

 - 2.0.3
   - Fixing event bugs

 - 2.0.2
   - Responsive charts

 - 2.0.1
   - Multiple chart support

 - 2.0.0
   - Its Here!

 - 2.0.0-beta1
   - Passed 75% test coverage
   - Added new options to TextStyle
     - Bold
     - Italic

 - 2.0.0-alpha4
   - Added Events
     - select
     - onmouseover
     - onmouseout

 - 2.0.0-alpha3
   - Added DataTable column formatters
     - DateFormat
     - NumberFormat

 - 2.0.0-alpha2
   - Added render method in favor of outputInto method
   - Added blade template extensions for seamless chart rendering
   - Moar tests!

 - 2.0.0-alpha1
   - Refactored the main Lavacharts class to not be static anymore (yay!)
   - Moved the creation of the javascript into it's own class
   - Added a new class "Volcano" to store all the charts.
   - Modfied the charts to not staticly call the Lavacharts functions
   - DataTables are no longer magic, but applied via method chaining
   - Added render method in favor of outputInto method
   - Added blade template extensions as aliases to the render method
   - Tests tests tests!
   - Using phpcs to bring all the code up to PSR2 standards
