LavaCharts
==========

LavaCharts is a graphing library for PHP5.3+ that wraps the Google Chart API

Branches
========
 - Master: [![Build Status](https://travis-ci.org/kevinkhill/LavaCharts.png?branch=master)](https://travis-ci.org/kevinkhill/LavaCharts)

 - Dev: [![Build Status](https://travis-ci.org/kevinkhill/LavaCharts.png?branch=dev)](https://travis-ci.org/kevinkhill/LavaCharts)

Installing
----------
In your project's main ```composer.json``` file, add this line to the requirements:

  ```
  "khill/lavacharts" : "1.0.*"
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
  $stocksTable = Lava::DataTable('Stocks');

  $stocksTable->addColumn('date', 'Date', 'date')
              ->addColumn('number', 'Projected', 'projected')
              ->addColumn('number', 'Closing', 'closing');

  for($a = 1; $a < 30; $a++)
  {
      $data = array(
          Lava::jsDate(2011, 5, $a), //Date
          rand(9500,10000),          //Column 1's data
          rand(9500,10000)           //Column 2's data
      );

      $stocksTable->addRow($data);
  }

  Lava::LineChart('Stocks')->title('Stock Market Trends');
  ```

View
====
  ```
  echo Lava::LineChart('Stocks')->outputInto('stocks');
  ```

This is assuming you already have a div in your page with the id "stocks":
```<div id="stocks"></div>```

Changelog
---------
 - v1.0.0
   - Refactored the main Lavacharts class to not be static anymore
   - Moved the creation of the javascript into it's own class
   - Added a new class "Volcano" to hold all the LavaCharts and DataTables
   - Modfied the charts to not staticly call the Lavacharts functions in favor
     of dependancy injection with the Volcano class
   - Tests tests tests!
   - Used phpcs to bring all the code up to PSR2 standards
