lava.events.on('jsapi:ready', function (google) {
    /**
     * If the object does not exist for a given chart type, initialize it.
     * This will prevent overriding keys when multiple charts of the same
     * type are being rendered on the same page.
     */
    if ( typeof lava.charts.<chartType> == "undefined" ) {
        lava.charts.<chartType> = {};
    }

    //Creating a new lavachart object
    lava.charts.<chartType>["<chartLabel>"] = new lava.Chart();

    //Checking if output div exists
    if (! document.getElementById("<elemId>")) {
        throw new Error('[Lavacharts] No matching element was found with ID "<elemId>"');
    }

    lava.charts.<chartType>["<chartLabel>"].render = function (data) {
        var $this = lava.charts.<chartType>["<chartLabel>"];

        $this.data = new <dataClass>(<chartData>, <dataVer>);

        $this.options = <chartOptions>;

        $this.chart = new <chartClass>(document.getElementById("<elemId>"));

        <formats>

        <events>

        $this.chart.draw($this.data, $this.options);
    };

    lava.charts.<chartType>["<chartLabel>"].setData = function (data) {
        var $this = lava.charts.<chartType>["<chartLabel>"];

        $this.data = new <dataClass>(data, <dataVer>);
    };

    lava.charts.<chartType>["<chartLabel>"].redraw = function () {
        var $this = lava.charts.<chartType>["<chartLabel>"];

        $this.chart.draw($this.data, $this.options);
    };

    lava.registerChart("<chartType>", "<chartLabel>");

    google.load('visualization', '<chartVer>', {
        packages: ['<chartPackage>'],
        callback: function() {
            lava.charts.<chartType>["<chartLabel>"].render();
        }
    });
});
