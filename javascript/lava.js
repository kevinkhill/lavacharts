window.lava = (function() {
  this.get              = null;
  this.event            = null;
  this.loadData         = null;
  this.register         = null;
  this.getLavachart     = null;
  this.charts           = {};
  this.registeredCharts = [];

  this.Chart = function() {
    this.init    = null;
    this.redraw  = null;
    this.setData = null;
    this.data    = null;
    this.chart   = null;
    this.options = null;
    this.formats = [];
  };

  this.get = function (chartLabel, callback) {
    if (arguments.length < 2 || typeof chartLabel !== 'string' || typeof callback !== 'function') {
      throw new Error('[Lavacharts] The syntax for lava.get must be (str ChartLabel, fn Callback)');
    }

    lava.getLavachart(chartLabel, function (lavachart) {
      return callback(lavachart.chart);
    });
  };

  this.loadData = function (chartLabel, dataTableJson, callback) {
    lava.getLavachart(chartLabel, function (lavachart) {
      lavachart.setData(dataTableJson);
      lavachart.redraw();

      if (typeof callback == "function") {
        return callback(lavachart.chart);
      } else {
        return true;
      }
    });
  };

  this.event = function (event, chart, callback) {
    return callback(event, chart);
  };

  this.register = function(type, label) {
    this.registeredCharts.push(type + ':' + label);
  };

  this.getLavachart = function (chartLabel, callback) {
    var chartTypes = Object.keys(lava.charts);
    var chart;

    var search = chartTypes.some(function (e) {
      if (typeof lava.charts[e][chartLabel] !== 'undefined') {
        chart = lava.charts[e][chartLabel];

        return true;
      } else {
        return false;
      }
    });

    if (search === false) {
      throw new Error('[Lavacharts] Chart "' + chartLabel + '" was not found');
    } else {
      callback(chart);
    }
  };

  this.redrawCharts = function() {
    var timer, delay = 300;

    clearTimeout(timer);

    timer = setTimeout(function() {
      for(var c = 0; c < lava.registeredCharts.length; c++) {
        var parts = lava.registeredCharts[c].split(':');

        lava.charts[parts[0]][parts[1]].chart.draw(
          lava.charts[parts[0]][parts[1]].data,
          lava.charts[parts[0]][parts[1]].options
        );
      }
    }, delay);
  };

  return this;
})();

window.addEventListener("resize", window.lava.redrawCharts);
