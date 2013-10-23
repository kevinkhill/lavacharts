LavaCharts
==========

LavaCharts is a Package for Laravel 4 / Composer that wraps the Google Chart API for PHP5.3+

[![Build Status](https://travis-ci.org/kevinkhill/LavaCharts.png?branch=master)](https://travis-ci.org/kevinkhill/LavaCharts)

#Installing
In your project's main ```composer.json``` file, add this line to the requirements:  

  ```
  "khill/lavacharts" : "dev-master"
  ```  

Run Composer to install LavaCharts:  

  ```
  composer install
  ```

Next, register LavaCharts in your app by adding this line to the providers array in ```app/config/app.php```:  

  ```
  "Khill\Lavacharts\LavachartsServiceProvider"
  ```

*Don't worry about the Alias, it is set up for you :)*

If you want to view the demos, publish the assets with:

  ```
  php artisan asset:publish khill/lavacharts
  ```
  
If you want to overide the default config of the package, run:

  ```
  php artisan config:publish khill/lavacharts
  ```
  
