/* jshint undef: true, unused: true */
/* globals lava, google */

(function(){
    "use strict";

    var $lava = this.lava;

    var $chart = $lava.createChart('<type>', '<label>');

    $chart.init = function() {
        $chart.package = '<package>';
        $chart.setElement('<elemId>');
        $chart.setPngOutput(<pngOutput>);

        $chart.configure = function () {
            $chart.render = function (data) {
                $chart.setData(<datatable>);

                $chart.options = <options>;

                $chart.chart = new <class>($chart.element);

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

        $lava.emit('ready', $chart);
    };

    $lava.store($chart);
}.apply(window));
