- 3.1.9
  - Fixing bug where using `setOptions` instead of the constructor skipped the processing of `png` and `material` attributes.

- 3.1.8
  - Production build of the Lava.js module.

- 3.1.7
  - Added the tag lavacharts to the config publishing.
    Use `php artisan vendor:publish --tag=lavacharts`
    If that does not work, try to clear the cache with `php artisan config:clear` and re-publish with `--force`.

- 3.1.6
  - The event callback within lava.js was modified to pass back the chart and the datatable so users can interact with either during an event. This solves issue [#203](https://github.com/kevinkhill/lavacharts/issues/203)
  
- 3.1.5
  - Adding DonutChart alias class back

- 3.1.4 
  - Chart's should resize properly on page resize.
  
- 3.1.3 
  - Adding support for date columns to be null which enables support for Gantt charts to have linked sections.
  - Adding JavascriptDate class that mimics the way the Javascript Date object is created. (I wanted to be able to copy and paste google's examples into addRows)
  
- 3.1.1 & 3.1.2
  - Adding back and repairing the Symfony Bundle
  
- 3.1.0
  - Public Release

- 3.1.0-beta2
  - Adjusting elementId precedence to prevent backwards compatability breaks and enable new features. The order of precedence goes as follows:
    - An elementId string passed to the render method will override an elementId set to the chart.
    - It is recommended to move all of the elementId strings on `render()` methods into the constructor, as an option: `['elementId' => 'render-to-div']`
    - Setting the elementId as an option during creation is preferred, to enable the use of the new feature, `renderAll()`, that will output all defined charts to the page. 
    
- 3.1.0-beta1
  - Adding setLocale() method for changing language of charts.
  
- 3.1.0-alpha
  - Adding more supported chart types
    - AnnotationChart
    - BubbleChart
    - CandlestickChart
    - HistogramChart
    - SankeyChart
    - SteppedAreaChart
    - TimelineChart
    - TreemapChart
  - Adding DataFactory with arrayToDataTable() method to try and automatically
    create columns and rows based on an array of data with labels. This mimics how
    Google's javascript version of the method works for creating DataTables more
    efficiently. Currently only works with strings and numbers.
    - Adding DataTable() method to the DataFactory as a shortcut to chaining methods.
      - The method has three signatures:
      - No params for an empty, default timezone DataTable
      - String param for setting the timezone
      - Array of columns and array of rows as 1st and 2nd for a complete DataTable in
        one method call. (The third parameter can also be used to set the timezone.)
    - [DataFactory Examples](https://gist.github.com/kevinkhill/0c7c5f6211c7fd8f9658)
  - Massive overhaul of lava.js module.
    - Refactored and using browserify to compile
    - Added `lava.ready()` method for wrapping any lava.js interaction. When given a function
      it will be called after all charts have rendered onto the page. Useful for delaying ajax requests
      until the chart is ready.
    - Added the ability to render directly as PNGs instead of SVGs
      - Just pass into the options `['png' => true]`
  - Created examples for each chart, available to view locally using the built in PHP server.
    - Navigate to the Examples folder in the lavacharts package folder. If you installed with Composer, then it
      should be at `$PROJECT_ROOT/vendor/khill/lavacharts/tests/Examples`
    - Use the given config and router to start the examples page `php -S 127.0.0.1:8000 -c php.ini router.php`
  - All exceptions now extend LavaException if the user wants to have a catch all
    - Utils class removed, broken into traits, and applied to classes that needed the methods

- 3.0.4
  - Fixing bug where TreeMap was not in list of chartClasses

- 3.0.3
  - Events Bugfix

- 3.0.2
  - Blade template extension bug fixes
  - Combining the `customize` method into the constructor to provide
    restriction free option setting without the extra method call.
  
- 3.0.1
  - Bug fixes
  
- 3.0.0
  - Dropping support for PHP 5.3
    - Minimum version PHP 5.4+
  - Added Dashboards
      - ChartWrappers
      - ControlWrappers
      - Added filters for Dashboard ControlWrappers
       - Category
       - ChartRange
       - DateRange
       - NumberRange
       - String
  - Chart Improvements
    - Assign DataTable and options via constructor.
    - Refactored all ConfigObject creation into the classes, no more manually instantiation.
    - Removed Event classes in favor of associative array definitions of events.
  - DataTable Improvements
    - Added support for csv file read/write and Laravel collection parsing into DataTables,
      just add the separate package to composer "khill/datatableplus":"dev-master".
      DataTablePlus extends the DataTable to add the extra functions and Lavacharts will seamlessly
      create DataTablePluses over DataTables if available via composer.
  - Added ScatterChart & TableChart
  - Added Format#formatColumn method to format datatable columns.
  - Added new formats.
   - ArrowFormat
   - BarFormat
  - lava.js has been refactored:
    - lava.get() replaced with:
      - getChart(label, callback) -> callback(Google chartObj, Lava chartObj)
        - Google chart object allows for using google's documented chart methods
        - Lava chart object contains all relevant info about the chart
         - chart, data, options, formats etc...
      - getDashboard(label, callback) -> callback(Google dashboardObj, Lava dashboardObj)
        - Google dashboard object allows for using google's documented dashboard methods
        - Lava dashboard object contains all relevant info about the dashboard
         - dashboard, control bindings, chart wrapper, data, options, etc...
    - lava.ready() used to wrap other lava.js interaction.
      - Called after all of the google jsapi is loaded and the charts are rendered.

- 2.5.7
 - AJAX data loading bugfixes

- 2.5.6
  - Fixes for AJAX chart loading

- 2.5.5
  - Blade extensions fix

- 2.5.4
  - Fixed namespace bug

- 2.5.3
  - Added column roles

- 2.5.2
  - AddedTimeOfDay columns

- 2.5.1
  - Lava.js bug fixes

- 2.5.0
  - Ajax chart loading

- 2.4.2
  - Added Lava#exists() method for checking if a chart exists

- 2.4.1
  - Added focusTarget to Line and Area charts

- 2.4.0
  - BarCharts added
  - Cleaned up code to PSR2
  - Changed from PSR0 -> PSR4

- 2.3.0
  - Added jsapi() method for manual script placement.

- 2.2.1
  - Timezone fixes
     Now they can be set with the constructor when creating a DataTable.

- 2.2.0
  - Gauge Chart support

- 2.1.0
  - Calendar Chart support

- 2.0.5
  - Updated Carbon
  - Laravel 5 compatibility

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
  - Modified the charts to not statically call the Lavacharts functions
  - DataTables are no longer magic, but applied via method chaining
  - Added render method in favor of outputInto method
  - Added blade template extensions as aliases to the render method
  - Tests tests tests!
  - Using phpcs to bring all the code up to PSR2 standards
