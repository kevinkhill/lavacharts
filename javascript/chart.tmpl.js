lava.on('jsapi:ready', function (google) {
    lava.initChartType("<chartType>");

    lava.storeChart(new lava.Chart("<chartType>", "<chartLabel>"));

    lava.getChart("<chartLabel>", function (gChart, lavaChart) {
        lavaChart.setElement("<elemId>");

        lavaChart.render = function (data) {
            this.data = new <dataClass>(<chartData>, <dataVer>);

            this.options = <chartOptions>;

            this.chart = new <chartClass>(this.element);

            <formats>

            <events>

            this.chart.draw(this.data, this.options);
        };

        lava.registerChart("<chartType>", "<chartLabel>");

        google.load('visualization', '<chartVer>', {
            packages: ['<chartPackage>'],
            callback: function() {
                lavaChart.render();
            }
        });
    });
});
