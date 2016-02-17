lava.on('jsapi:ready', function (google) {
    var dash = new lava.Dashboard("<label>");

    dash.setElement('<elemId>');

    dash.render = function() {
        this.dashboard = new <class>(this.element);

        this.setData(<chartData>);

        <bindings>

        this.dashboard.draw(this.data);
    };

    lava.storeDashboard(dash);

    google.load('visualization', '<version>', {
        packages: <packages>,
        callback: function() {
            lava.getDashboard('<label>', function (dash) {
                dash.render();
            });
        }
    });
});
