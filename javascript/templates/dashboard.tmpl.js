/* jshint undef: true, unused: true */
/* globals lava, google */

(function(){
    "use strict";

    var $dash = lava.createDashboard('<label>');

    $dash.init = function () {
        $dash.setElement('<elemId>');
        $dash.packages = <packages>;

        $dash.configure = function () {
            $dash.render = function (data) {
                $dash.dashboard = new <class>($dash.element);

                $dash.setData(<chartData>);

                <bindings>

                $dash.dashboard.draw($dash.data);

                lava.emit('rendered', $dash);
            };

            $dash.deferred.resolve();
            return $dash.deferred.promise;
        };

        lava.emit('ready', $dash);
    };

    lava.store($dash);
})();
