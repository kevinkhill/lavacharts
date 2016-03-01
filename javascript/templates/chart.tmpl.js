lava.on('jsapi:ready', function (google) {
    var chart = new lava.Chart('<chartType>', '<chartLabel>');

    chart.setElement('<elemId>');
    chart.setPngOutput(<pngOutput>);

    chart.render = function (data) {
        this.setData(<chartData>);

        this.options = <chartOptions>;

        this.chart = new <chartClass>(this.element);

        <formats>
        <events>

        this.chart.draw(this.data, this.options);

        if (this.pngOutput === true) {
            //window.google.visualization.events.addListener(this.chart, 'ready', this.drawPng);
            this.drawPng();
        }

        lava.emit('rendered');
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
