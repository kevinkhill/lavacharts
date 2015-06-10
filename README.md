# Lavacharts [![Total Downloads](https://img.shields.io/packagist/dt/khill/lavacharts.svg?style=plastic)](https://packagist.org/packages/khill/lavacharts) [![License](https://img.shields.io/packagist/l/khill/lavacharts.svg?style=plastic)](http://opensource.org/licenses/MIT) [![PayPayl](https://img.shields.io/badge/paypal-donate-yellow.svg?style=plastic)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FLP6MYY3PYSFQ)

Lavacharts is a graphing / chart library for PHP5.3+ that wraps the Google Chart API


Stable:
[![Current Release](https://img.shields.io/github/release/kevinkhill/lavacharts.svg?style=plastic)](https://github.com/kevinkhill/lavacharts/releases)
[![Build Status](https://img.shields.io/travis/kevinkhill/lavacharts/master.svg?style=plastic)](https://travis-ci.org/kevinkhill/lavacharts)
[![Coverage Status](https://img.shields.io/coveralls/kevinkhill/lavacharts/master.svg?style=plastic)](https://coveralls.io/r/kevinkhill/lavacharts?branch=master)



## Package Features
- Blade template extensions for laravel
- Lava.js module for interacting with charts
  - AJAX data reloading
  - Fetching charts
  - Events integration
- Datatable addColumn aliases
- Datatable column formatters
- Carbon support for date columns
- Supports string, number, date, and timeofday columns
- Now supporting 10 Charts!
  - Area, Bar, Calendar, Column, Combo, Donut, Gauge, Geo, Line, Pie

### For complete documentation, please visit [lavacharts.com](http://lavacharts.com/)


## Installing
In your project's main ```composer.json``` file, add this line to the requirements:

  ```
  "khill/lavacharts": "2.5.*"
  ```

Run Composer to install Lavacharts:

  ```
  composer update
  ```

## Laravel Service Provider
### Laravel 5.x
Register Lavacharts in your app by adding this line to the end of the providers array in ```config/app.php```:
  ```
  'providers' => [
      ...
      
      Khill\Lavacharts\Laravel\LavachartsServiceProvider::class
  ],
  ```
The ```Lava::``` alias will be registered automatically via the service provider.

### Laravel 4.x
Register Lavacharts in your app by adding this line to the end of the providers array in ```app/config/app.php```:

  ```
  'providers' => array(
      ...

      "Khill\Lavacharts\Laravel\LavachartsServiceProvider"
  ),
  ```
The ```Lava::``` alias will be registered automatically via the service provider.

## Non-Laravel
If you are using Lavacharts with Composer and not in Laravel, that's fine, just make sure to:
```require 'vendor/autoload.php';``` within you project.

Create an instance of Lavacharts: ```$lava = new Khill\Lavacharts\Lavacharts;```

Replace all of the ```Lava::``` aliases in the examples, by chaining from the Lavacharts object you created.

Ex: ```$dt = $lava->DataTable();``` instead of ```$dt = Lava::DataTable();```


# Usage
The creation of charts is separated into two parts:
First, within a route or controller, you define the chart, the data table, and the customization of the output.

Second, within a view, you use one line and the library will output all the necessary javascript code for you.

## Basic Example
Here is an example of the simplest chart you can create: A line chart with one dataset and a title, no configuration.

### Controller
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

## View
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
