Lavacharts
==========

Lavacharts is a graphing library for PHP5.3+ that wraps the Google Chart API

Branches
========
[![Build Status](https://travis-ci.org/kevinkhill/Lavacharts.png?branch=v2.0)](https://travis-ci.org/kevinkhill/Lavacharts) [![Coverage Status](https://coveralls.io/repos/kevinkhill/Lavacharts/badge.png?branch=v2.0)](https://coveralls.io/r/kevinkhill/Lavacharts?branch=v2.0) [![Total Downloads](https://poser.pugx.org/khill/lavacharts/downloads.svg)](https://packagist.org/packages/khill/lavacharts) [![License](https://poser.pugx.org/khill/lavacharts/license.svg)](https://packagist.org/packages/khill/lavacharts)

Installing
----------
In your project's main ```composer.json``` file, add this line to the requirements:

  ```
  "khill/lavacharts": "~2.0"
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

      "Lavacharts\LaravelServiceProvider"
  ),
  ```

  Don't worry about the ```Lava``` alias, the service provider registers it automatically.

If you want to view the demos, publish the assets with:

  ```
  php artisan asset:publish khill/lavacharts
  ```

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
  $stocksTable = Lava::DataTable();

  $stocksTable->addColumn('date', 'Date', 'date')
              ->addColumn('number', 'Projected', 'projected')
              ->addColumn('number', 'Closing', 'closing');

  for($day = 1; $day < 30; $day++)
  {
      $data = array(
          "5/$day/2014",     // Date string, DateTime Object, Carbon Object
          rand(9500,10000),  // Column 1's data (int | float)
          rand(9500,10000)   // Column 2's data (int | float)
      );

      $stocksTable->addRow($data);
  }

  Lava::LineChart('Stocks')
      ->dataTable($stocksTable)
      ->title('Stock Market Trends');
  ```

View
====
If you are using Laravel and the Blade templating engine, there are some nifty extensions thrown in for a cleaner view

  ```
  @linechart('Stocks', 'stocks-div');
  // Behind the scenes this just calls Lava::render('LineChart', 'Stocks', 'stocks-div')
  ```

Or you can use the new render method, passing in the chart type, label, and element id.

  ```
  echo Lava::render('LineChart', 'Stocks', 'stocks-div');
  ```

This is all assuming you already have a div in your page with the id "stocks-div":
```<div id="stocks-div"></div>```


Notice
======
If you are using Lavacharts with Composer and not in Laravel, that's fine, just make sure to:
```require 'vendor/autoload.php';``` within you project
Create an instance of Lavacharts: ```$lava = new Lavacharts\Lavacharts;```
Replace all of the ```Lava::``` class aliases in the examples, by chaining from the Lavacharts object you created.

example: Use ```$dt = $lava->DataTable();``` instead of ```$dt = Lava::DataTable();```

New Site & Docs
===============
I am working hard on creating the new site for Lavacharts with full documentation for all aspects of what the class can do for you :)

Changelog
---------
 - v2.0.0-alpha4
   - Added Events
     - select
     - onmouseover
     - onmouseout

 - v2.0.0-alpha3
   - Changed namespace
   - Added DataTable column formatters
     - DateFormat
     - NumberFormat

 - v2.0.0-alpha2
   - Added render method in favor of outputInto method
   - Added blade template extensions for seamless chart rendering
   - Moar tests!

 - v2.0.0-alpha1
   - Refactored the main Lavacharts class to not be static anymore (yay!)
   - Moved the creation of the javascript into it's own class
   - Added a new class "Volcano" to store all the charts.
   - Modfied the charts to not staticly call the Lavacharts functions
   - DataTables are no longer magic, but applied via method chaining
   - Added render method in favor of outputInto method
   - Added blade template extensions as aliases to the render method
   - Tests tests tests!
   - Using phpcs to bring all the code up to PSR2 standards
