# Lavacharts 3.1 Beta
## :smile: This is a work in progress, so there is no guarantee that it works :smile:
[![Total Downloads](https://img.shields.io/packagist/dt/khill/lavacharts.svg?style=plastic)](https://packagist.org/packages/khill/lavacharts)
[![License](https://img.shields.io/packagist/l/khill/lavacharts.svg?style=plastic)](http://opensource.org/licenses/MIT)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg?style=plastic)](https://php.net/)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/kevinkhill/lavacharts?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![PayPayl](https://img.shields.io/badge/paypal-donate-yellow.svg?style=plastic)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FLP6MYY3PYSFQ)

Lavacharts is a graphing / chart library for PHP5.4+ that wraps the Google Chart API

Stable:
[![Current Release](https://img.shields.io/github/release/kevinkhill/lavacharts.svg?style=plastic)](https://github.com/kevinkhill/lavacharts/releases)
[![Build Status](https://img.shields.io/travis/kevinkhill/lavacharts/3.0.svg?style=plastic)](https://travis-ci.org/kevinkhill/lavacharts)
[![Coverage Status](https://img.shields.io/coveralls/kevinkhill/lavacharts/3.0.svg?style=plastic)](https://coveralls.io/r/kevinkhill/lavacharts?branch=3.0)

Dev:
[![Development Release](https://img.shields.io/badge/release-3.1.x--dev-brightgreen.svg?style=plastic)](https://github.com/kevinkhill/lavacharts/tree/master)
[![Build Status](https://img.shields.io/travis/kevinkhill/lavacharts/master.svg?style=plastic)](https://travis-ci.org/kevinkhill/lavacharts)
[![Coverage Status](https://img.shields.io/coveralls/kevinkhill/lavacharts/master.svg?style=plastic)](https://coveralls.io/r/kevinkhill/lavacharts?branch=master)


## Package Features
- **Updated!** Any option for customizing charts that Google supports, Lavacharts should as well. Just use the chart constructor to assign any customization options you wish!
 - Visit [Google's Chart Gallery](https://developers.google.com/chart/interactive/docs/gallery) for details on available options
- Custom javascript module for interacting with charts client-side
  - AJAX data reloading
  - Fetching charts
  - Events integration
- Column Formatters & Roles
- Blade template extensions for Laravel
- Twig template extensions for Symfony
- [Carbon](https://github.com/briannesbitt/Carbon) support for date/datetime/timeofday columns
- Now supporting **22** Charts!
  - Annotation, Area, Bar, Bubble, Calendar, Candlestick, Column, Combo, Gantt, Gauge, Geo, Histogram, Line, Org, Pie, Sankey, Scatter, SteppedArea, Table, Timeline, TreeMap, and WordTree!
- [DataTablePlus](https://github.com/kevinkhill/datatableplus) package can be added to parse CSV files or Eloquent collections into DataTables.


#### For complete documentation, please visit [lavacharts.com](http://lavacharts.com/)
#### Upgrade guide: [Migrating from 2.5.x to 3.0.x](https://github.com/kevinkhill/lavacharts/wiki/Upgrading-from-2.5-to-3.0)
#### For contributing, a handy guide [can be found here](https://github.com/kevinkhill/lavacharts/blob/master/.github/CONTRIBUTING.md)

---

## Installing
In your project's main ```composer.json``` file, add this line to the requirements:
```json
"khill/lavacharts": "dev-3.1"
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

# Examples
For examples, open your favorite terminal and navigate to the lavacharts folder...
```bash
$ cd tests/Examples && php -S 127.0.0.1:8000 -c php.ini router.php
```
Then point your browser to ```127.0.0.1:8000``` and check out some charts


# Usage
The creation of charts is separated into two parts:
First, within a route or controller, you define the chart, the data table, and the customization of the output.

Second, within a view, you use one line and the library will output all the necessary javascript code for you.

## Basic Example
Here is an example of the simplest chart you can create: A line chart with one dataset and a title, no configuration.

### Controller
Setting up your first chart

#### Data
```php
$data = $lava->DataTable();

$data->addDateColumn('Day of Month')
            ->addNumberColumn('Projected')
            ->addNumberColumn('Official');

// Random Data For Example
for ($a = 1; $a < 30; $a++)
{
    $rowData = [
      "2014-8-$a", rand(800,1000), rand(800,1000)
    ];

    $data->addRow($rowData);
}
```

Arrays work for datatables as well...
```php
$data->addColumns([
    ['date', 'Day of Month'],
    ['number', 'Projected'],
    ['number', 'Official']
]];
```

Or you can ```use \Khill\Lavacharts\DataTables\DataFactory``` [to create DataTables in another way](https://gist.github.com/kevinkhill/0c7c5f6211c7fd8f9658)

#### Chart Options
Customize your chart, with any options found in google's documentation. Break objects down into arrays and pass to the chart.
```php
$lava->LineChart('Stocks', $data, [
    'title' => 'Stock Market Trends',
    'animation' => [
        'startup' => true,
        'easing' => 'inAndOut'
    ],
    'colors' => ['blue', '#F4C1D8']
]);
```

#### Output ID
The chart will needs to be output into a div on the page, so an html ID for a div is needed.
Here is where you want your chart ```<div id="stocks-div"></div>```
 - If no options for the chart are set, then the third parameter is the id of the output:
```php
  $lava->LineChart('Stocks', $data, 'stocks-div');
```
 - If there are options set for the chart, then the id may be included in the options:
```php
    $lava->LineChart('Stocks', $data, [
        'elementId' => 'stocks-div'
        'title' => 'Stock Market Trends'
    ]);
```
 - The 4th parameter will also work:
```php
    $lava->LineChart('Stocks', $data, [
        'title' => 'Stock Market Trends'
    ], 'stocks-div');
```


## View
Pass the main lavacharts instance to the view, because all of the defined charts are stored within, and render!
```php
<?= $lava->renderAll(); ?>
```


# Changelog
The complete changelog can be found [here](https://github.com/kevinkhill/lavacharts/wiki/Changelog)
