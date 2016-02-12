# Lavacharts v3.0
[![Total Downloads](https://img.shields.io/packagist/dt/khill/lavacharts.svg?style=plastic)](https://packagist.org/packages/khill/lavacharts)
[![License](https://img.shields.io/packagist/l/khill/lavacharts.svg?style=plastic)](http://opensource.org/licenses/MIT)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg?style=plastic)](https://php.net/)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/kevinkhill/lavacharts?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![PayPayl](https://img.shields.io/badge/paypal-donate-yellow.svg?style=plastic)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FLP6MYY3PYSFQ)

Lavacharts is a graphing / chart library for PHP5.4+ that wraps Google's Javascript Chart API

Stable:
[![Current Release](https://img.shields.io/github/release/kevinkhill/lavacharts.svg?style=plastic)](https://github.com/kevinkhill/lavacharts/releases)
[![Build Status](https://img.shields.io/travis/kevinkhill/lavacharts/3.0.svg?style=plastic)](https://travis-ci.org/kevinkhill/lavacharts)
[![Coverage Status](https://img.shields.io/coveralls/kevinkhill/lavacharts/3.0.svg?style=plastic)](https://coveralls.io/r/kevinkhill/lavacharts?branch=3.0)

Dev:
[![Development Release](https://img.shields.io/badge/release-dev--3.1-brightgreen.svg?style=plastic)](https://github.com/kevinkhill/lavacharts/tree/master)
[![Build Status](https://img.shields.io/travis/kevinkhill/lavacharts/master.svg?style=plastic)](https://travis-ci.org/kevinkhill/lavacharts)
[![Coverage Status](https://img.shields.io/coveralls/kevinkhill/lavacharts/master.svg?style=plastic)](https://coveralls.io/r/kevinkhill/lavacharts?branch=master)

## Version 3.0
Upgrade guide: [Migrating from 2.5.x to 3.0.x](https://github.com/kevinkhill/lavacharts/wiki/Upgrading-from-2.5-to-3.0)

## Package Features
- Lava.js module for interacting with charts client-side
  - AJAX data reloading
  - Fetching charts
  - Events integration
- Column Formatters
- Column Roles
- Blade template extensions for laravel
- Twig template extensions for Symfony
- [Carbon](https://github.com/briannesbitt/Carbon) support for date/datetime/timeofday columns
- Now supporting 12 Charts!
  - Area, Bar, Calendar, Column, Combo, Donut, Gauge, Geo, Line, Pie, Scatter, Table
- [DataTablePlus](https://github.com/kevinkhill/datatableplus) package can be added to parse CSV files or Eloquent collections into DataTables.

## For complete documentation, please visit [lavacharts.com](http://lavacharts.com/)

---

## Installing
In your project's main ```composer.json``` file, add this line to the requirements:
```json
"khill/lavacharts": "~3.0"
```

Run Composer to install Lavacharts:
```bash
$ composer update
```

## Framework Agnostic
If you are using Lavacharts with Silex, Lumen or your own Composer project, that's no problem! Just make sure to:
```require 'vendor/autoload.php';``` within you project and create an instance of Lavacharts: ```$lava = new Khill\Lavacharts\Lavacharts;```


## Laravel
To integrate lavacharts into Laravel, a ServiceProvider has been included.

### Laravel 5.x
Register Lavacharts in your app by adding this line to the end of the providers array in ```config/app.php```:
```php
<?php
// config/app.php

// ...
'providers' => [
    ...

    Khill\Lavacharts\Laravel\LavachartsServiceProvider::class,
],
```
The ```Lava::``` alias will be registered automatically via the service provider.

### Laravel 4.x
Register Lavacharts in your app by adding this line to the end of the providers array in ```app/config/app.php```:

```php
<?php
// app/config/app.php

// ...
'providers' => array(
    // ...

    "Khill\Lavacharts\Laravel\LavachartsServiceProvider",
),
```
The ```Lava::``` alias will be registered automatically via the service provider.


## Symfony 2.x
Also included is a Bundle for Symfony to create a service that can be pulled from the Container.

### Add Bundle
Add the bundle to the AppKernel:
```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Khill\Lavacharts\Symfony\Bundle\LavachartsBundle(),
        );

        // ...
    }

    // ...
}
```
### Import Config
Add the service definition to the ```app/config/config.yml``` file
```yaml
imports:
  # ...
  - { resource: @LavachartsBundle/Resources/config/services.yml
```



# Usage
The creation of charts is separated into two parts:
First, within a route or controller, you define the chart, the data table, and the customization of the output.

Second, within a view, you use one line and the library will output all the necessary javascript code for you.

## Basic Example
Here is an example of the simplest chart you can create: A line chart with one dataset and a title, no configuration.

### Controller
```php
    $stocksTable = $lava->DataTable();  // Lava::DataTable() if using Laravel

    $stocksTable->addDateColumn('Day of Month')
                ->addNumberColumn('Projected')
                ->addNumberColumn('Official');

    // Random Data For Example
    for ($a = 1; $a < 30; $a++)
    {
        $rowData = [
          "2014-8-$a", rand(800,1000), rand(800,1000)
        ];

        $stocksTable->addRow($rowData);
    }
```

Arrays work for datatables as well...
```php
  $stocksTable->addColumns([
    ['date', 'Day of Month'],
    ['number', 'Projected'],
    ['number', 'Official']
  ]];
```

...and for setting chart options!
```
  $lava->LineChart('Stocks', $stocksTable, ['title' => 'Stock Market Trends']);
```

## View
If you are using Laravel and the Blade templating engine, there are some nifty extensions thrown in for a cleaner view

```php
@linechart('Stocks', 'stocks-div');
// Behind the scenes this just calls Lava::renderLineChart('Stocks', 'stocks-div')
// which is an alias for the render method, seen below
```

Or you can use the new render method, passing in the chart type, label, and element id.

```php
echo Lava::render('LineChart', 'Stocks', 'stocks-div');
```

This is all assuming you already have a div in your page with the id "stocks-div":
```<div id="stocks-div"></div>```

If you don't have a div ready to accept the charts, add one more parameter to ```@linechart()``` or ```render()``` and it will be created for you.

Add ```true``` to for the library to create a plain div, or an array with keys ```width & height```

Example:
```php
@linechart('Stocks', 'stocks-div', true)
// Or
echo Lava::render('LineChart', 'Stocks', 'stocks-div', ['width'=>1024, 'height'=>768]);
```

# Changelog
The complete changelog can be found [here](https://github.com/kevinkhill/lavacharts/wiki/Changelog)
