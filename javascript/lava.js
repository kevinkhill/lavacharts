var lava = {
  get              : null,
  event            : null,
  loadData         : null,
  register         : null,
  getLavachart     : null,
  charts           : {},
  dashboards       : {},
  registeredCharts : [],

  Chart: function() {
    this.draw    = null;
    this.data    = null;
    this.chart   = null;
    this.options = null;
    this.formats = [];
  },
  Dashboard: function() {
    this.draw      = null;
    this.data      = null;
    this.bindings  = [];
    this.dashboard = null;
  },
  get: function (chartLabel, callback) {
    if (arguments.length < 2 || typeof chartLabel !== 'string' || typeof callback !== 'function') {
      throw new Error('[Lavacharts] The syntax for lava.get must be (str ChartLabel, fn Callback)');
    }

    lava.getLavachart(chartLabel, function (lavachart) {
      return callback(lavachart.chart);
    });
  },
  loadData: function (chartLabel, dataTableJson, callback) {
    lava.getLavachart(chartLabel, function (lavachart) {
      lavachart.data = new google.visualization.DataTable(dataTableJson, '0.6');

      lavachart.chart.draw(lavachart.data, lavachart.options);

      return callback(lavachart.chart);
    });
  },
  event: function (event, chart, callback) {
    return callback(event, chart);
  },
  register: function(type, label) {
    this.registeredCharts.push(type + ':' + label);
  },
  getLavachart: function (chartLabel, callback) {
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
  },
  redrawCharts: function() {
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
  }
};

window.addEventListener("resize", window.lava.redrawCharts);
