/* jshint undef: true, unused: true */
/* globals lava, google */

(function(){
    "use strict";

    var $chart = lava.createChart('<chartType>', '<chartLabel>');

    $chart.init = function() {
        $chart.package = '<chartPackage>';
        $chart.setElement('<elemId>');
        $chart.setPngOutput(<pngOutput>);

        $chart.configure = function () {
            $chart.render = function (data) {
                $chart.setData(<chartData>);

                $chart.options = <chartOptions>;

                $chart.chart = new <chartClass>($chart.element);

                <formats>
                <events>

                $chart.chart.draw($chart.data, $chart.options);

                if ($chart.pngOutput === true) {
                    $chart.drawPng();
                }

                $chart.promises.rendered.resolve();
                return $chart.promises.rendered.promise;
            };

            $chart.promises.configure.resolve();
            return $chart.promises.configure.promise;
        };

        lava.emit('ready', $chart);
    };

    lava.store($chart);
})();
