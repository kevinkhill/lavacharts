LavaCharts
==========

LavaCharts is a graphing library for PHP5.3+ that wraps the Google Chart API

Branches
========
[![Build Status](https://travis-ci.org/kevinkhill/LavaCharts.png?branch=v2.0)](https://travis-ci.org/kevinkhill/LavaCharts) [![Coverage Status](https://coveralls.io/repos/kevinkhill/LavaCharts/badge.png?branch=v2.0)](https://coveralls.io/r/kevinkhill/LavaCharts?branch=v2.0) [![Total Downloads](https://poser.pugx.org/khill/lavacharts/downloads.svg)](https://packagist.org/packages/khill/lavacharts) [![License](https://poser.pugx.org/khill/lavacharts/license.svg)](https://packagist.org/packages/khill/lavacharts)

Installing
----------
In your project's main ```composer.json``` file, add this line to the requirements:

  ```
  "khill/lavacharts": "2.0.*@dev"
  ```

Run Composer to install LavaCharts:

  ```
  composer update
  ```

For Laravel
-----------
Register LavaCharts in your app by adding this line to the providers array in ```app/config/app.php```:

  ```
  "Khill\Lavacharts\LavachartsServiceProvider"
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
Here is an example of the simplest chart you can create: A line chart with one dataset and no customizations.

Controller
==========
  ```
  $stocksTable = Lava::DataTable();

  $stocksTable->addColumn('date', 'Date', 'date')
              ->addColumn('number', 'Projected', 'projected')
              ->addColumn('number', 'Closing', 'closing');

  for($a = 1; $a < 30; $a++)
  {
      $data = array(
          Carbon::create(2011, 5, $a), //Date
          rand(9500,10000),            //Column 1's data
          rand(9500,10000)             //Column 2's data
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


Changelog
---------
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
