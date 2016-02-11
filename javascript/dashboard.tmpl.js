lava.events.on('jsapi:ready', function (google) {
    //Checking if dashboard div exists
    if (! document.getElementById("<elemId>")) {
        throw new Error('[Lavacharts] No matching element was found with ID "<elemId>"');
    }

    lava.dashboards["<label>"] = new lava.Dashboard();

    lava.dashboards["<label>"].render = function() {
        var $this = lava.dashboards["<label>"];

        $this.dashboard = new <class>(document.getElementById('<elemId>'));

        $this.data = new <dataClass>(<chartData>, <dataVer>);

        <bindings>

        $this.dashboard.draw($this.data);
    };

    google.load('visualization', '<version>', {
        packages: <packages>,
        callback: function() {
            lava.dashboards["<label>"].render();
        }
    });
});
