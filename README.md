LavaCharts
==========

LavaCharts is a graphing library that wraps the Google Chart API for PHP5.3+


[![Build Status](https://travis-ci.org/kevinkhill/LavaCharts.png?branch=master)](https://travis-ci.org/kevinkhill/LavaCharts)
  
Installing
-----------
In your project's main ```composer.json``` file, add this line to the requirements:  

  ```
  "khill/lavacharts" : "dev-master"
  ```  

Run Composer to install LavaCharts:  

  ```
  composer update
  ```

Next, register LavaCharts in your app by adding this line to the providers array in ```app/config/app.php```:  

  ```
  "Khill\Lavacharts\LavachartsServiceProvider"
  ```

  Don't worry about the alias, the service provider registers it automatically.

If you want to view the demos, publish the assets with:

  ```
  php artisan asset:publish khill/lavacharts
  ```  
  
Usage
-----
The creation of charts is separated into two pieces:  
First, within a route or controller, you defining the chart, the data table, and the customization of the output.

Second, within a view or view composer, you use the library to output all the necessary javascript code for you.
  
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
  echo Lava::LineChart('Stocks')->outputInto('stock_div');
  echo Lava::div(600, 400);

  if(Lava::hasErrors())
  {
      echo Lava::getErrors();
  }
  ```