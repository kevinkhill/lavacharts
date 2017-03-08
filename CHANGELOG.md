- 3.0.7
  - Added support for Traversable and ArrayAccess when adding rows to datatables.

- 3.0.6
  - Refactored the LavachartsServiceProvider to use `singleton` over `share` since it was removed in Laravel 5.4

- 3.0.5
  - Fixing bug in Blade template exension.

- 3.0.4
  - Fixing bug where TreeMap was not in list of chartClasses

- 3.0.3
  - Events bugfix

- 3.0.2
  - Blade template extension bug fixes
  - Combining the ```customize``` method into the constructor to provide
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
  - Modfied the charts to not staticly call the Lavacharts functions
  - DataTables are no longer magic, but applied via method chaining
  - Added render method in favor of outputInto method
  - Added blade template extensions as aliases to the render method
  - Tests tests tests!
  - Using phpcs to bring all the code up to PSR2 standards
