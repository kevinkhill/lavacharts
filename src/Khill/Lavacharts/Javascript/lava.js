function onResize (c, t) {
  window.onresize = function() {
    clearTimeout(t);
    t = setTimeout(c, 100);
  };

  return c;
};

var lava = lava || {
  get              : null,
  event            : null,
  charts           : {},
  registeredCharts : []
};

lava.get = function (chartLabel) {
    var chartTypes = Object.keys(lava.charts),
        chart;

    if (typeof chartLabel === 'string') {
        if (Array.isArray(chartTypes)) {
            chartTypes.some(function (e) {
                if (typeof lava.charts[e][chartLabel] !== 'undefined') {
                    chart = lava.charts[e][chartLabel].chart;

                    return true;
                } else {
                    return false;
                }
            });
        } else {
            return false;
        }
    } else {
        console.error('[Lavacharts] The input for lava.get() must be a string.');

        return false;
    }
};

lava.event = function (event, chart, callback) {
    return callback(event, chart);
};

lava.register = function(type, label) {
    this.registeredCharts.push(type + ':' + label);
};

window.onload = function() {
    onResize(function() {
        for(var c = 0; c < lava.registeredCharts.length; c++) {
            var parts = lava.registeredCharts[c].split(':');

            lava.charts[parts[0]][parts[1]].draw();
        }
    });
};