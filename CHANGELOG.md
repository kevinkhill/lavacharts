- 3.1.0
  - Removing all option checking since Google is much faster at updating
    their charts and options. I can't keep up so instead, it's now up to
    the user to check Google's documentation for valid options and types.
    - Removing the option checking made it much easier to create new chart
      classes. Only a few lines and they just work.
    - Because of this, Lavacharts now supports
      - AnnotationChart
      - BubbleChart
      - CandlestickChart
      - HistogramChart
      - SankeyChart
      - SteppedAreaChart
      - TimelineChart
      - TreemapChart
  - The output element ID of the chart is now a property of the chart, and is no
    longer passed to the render method. When rendering the chart, the element ID
    assigned during creation will be used.
    - If no options are needed on the chart, then it is the 3rd parameter.
    - If options are used, then it is the 4th parameter OR it can be assigned
      with the options as [ 'elementId' => 'render-chart-here' ]
    - If not assigned by the user, then the chart's label will be converted to the
      elementId. It will be all lowercase, with any special characters replaced with
      hyphens.
      - For example: Price Of Goods => price-of-goods and Fancy+Chart! => fancy-chart
  - Adding DataFactory with arrayToDataTable() method to try and automatically
    create columns and rows based on an array of data with labels. This mimics how
    Google's javascript version of the method works for creating DataTables more
    efficiently. Currently only works with strings and numbers.
    - [Example Gist](https://gist.github.com/kevinkhill/b4b0cccb832250e227c0)
  - Adding DataTable() method to the DataFactory as a shortcut to chaining methods.
    - The method has three signatures:
    - No params for an empty, default timezone DataTable
    - String param for setting the timezone
    - Array of columns and array of rows as 1st and 2nd for a complete DataTable in
      one method call. (The third paramater can also be used to set the timezone.)
  - All exceptions now extend LavaException if the user wants to have a catch all
  - Utils class removed, broken into traits, and applied to classes that needed the methods

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
