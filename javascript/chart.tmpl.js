lava.on('jsapi:ready', function (google) {
    var chart = new lava.Chart('<chartType>', '<chartLabel>');

    chart.setElement('<elemId>');

    chart.render = function (data) {
        this.setData(<chartData>);

        this.options = <chartOptions>;

        this.chart = new <chartClass>(this.element);

        <formats>

        <events>

        this.chart.draw(this.data, this.options);
    };

    lava.storeChart(chart);

    google.load('visualization', '<chartVer>', {
        packages: ['<chartPackage>'],
        callback: function() {
            lava.getChart('<chartLabel>', function (chart) {
                chart.render();
            });
        }
    });
});
