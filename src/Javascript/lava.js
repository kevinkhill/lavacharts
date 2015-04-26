(function() {
  var root = this;

  var lava = {
    get              : null,
    event            : null,
    loadData         : null,
    register         : null,
    getLavachart     : null,
    charts           : {},
    registeredCharts : []
  };

  lava.get = function (chartLabel, callback) {
    if (arguments.length < 2 || typeof chartLabel !== 'string' || typeof callback !== 'function') {
      throw new Error('[Lavacharts] The syntax for lava.get must be (str ChartLabel, fn Callback)');
    }

    lava.getLavachart(chartLabel, function (lavachart) {
      return callback(lavachart.chart);
    });
  };

  lava.loadData = function (chartLabel, dataTableJson, callback) {
    lava.getLavachart(chartLabel, function (lavachart) {
      lavachart.data = new google.visualization.DataTable(dataTableJson, '0.6');

      lavachart.chart.draw(lavachart.data, lavachart.options);

      return callback(lavachart.chart);
    });
  };

  lava.event = function (event, chart, callback) {
    return callback(event, chart);
  };

  lava.register = function(type, label) {
    this.registeredCharts.push(type + ':' + label);
  };

  lava.getLavachart = function (chartLabel, callback) {
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


  if (typeof exports !== 'undefined' ) {
    if (typeof module !== 'undefined' && module.exports) {
      exports = module.exports = lava;
    }

    exports.lava = lava;
  } else {
    root.lava = lava;

    window.onresize = function() {
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
  }
}.call(this));