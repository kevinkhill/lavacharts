lava.on('jsapi:ready', function (google) {
    var chart = new lava.Chart('<chartType>', '<chartLabel>');

    chart.setElement('<elemId>');

    chart.render = function (data) {
        this.data = new <dataClass>(<chartData>, <dataVer>);

        this.options = <chartOptions>;

        this.chart = new <chartClass>(this.element);

        <formats>

        <events>

        this.chart.draw(this.data, this.options);
    };

    lava.storeAndRegisterChart(chart);

    google.load('visualization', '<chartVer>', {
        packages: ['<chartPackage>'],
        callback: function() {
            lava.getChart('<chartLabel>', function (gChart, chart) {
                chart.render();
            });
        }
    });
});
