var lava = lava || {
  get              : null,
  event            : null,
  loadData         : null,
  charts           : {},
  registeredCharts : []
};

lava.get = function (chartLabel, callback) {
  var error, lavachart, chartTypes = Object.keys(lava.charts);

  if (typeof chartLabel === 'string') {
    chartTypes.some(function (e) {
      if (typeof lava.charts[e][chartLabel] !== 'undefined') {
        if (callback.length == 1) {
          lavachart = lava.charts[e][chartLabel];
        }
      } else {
        error = 'Chart "' + chartLabel + '" was not found.';
      }
    });
  } else {
    error = 'The arguments for lava.get must be (str chartLabel [, func Callback ]).';
  }

  if (callback.length == 1) {
    callback(lavachart);
  }

  if (callback.length == 2) {
    callback(error, lavachart);
  }
};

lava.loadData = function (chartLabel, dataTableJson, callback) {
  lava.get(chartLabel, function (lava) {
    lava.data = new google.visualization.DataTable(dataTableJson, '0.6');

    lava.chart.draw(lava.data, lava.options);

    callback(lava.chart);
  });
};

lava.event = function (event, chart, callback) {
  return callback(event, chart);
};

lava.register = function(type, label) {
  this.registeredCharts.push(type + ':' + label);
};

window.onload = function() {
  var timer, delay = 500;

  window.onresize = function() {
    clearTimeout(timer);

    timer = setTimeout(function() {
      for(var c = 0; c < lava.registeredCharts.length; c++) {
        var parts = lava.registeredCharts[c].split(':');

        console.log('redrawing...');
        lava.charts[parts[0]][parts[1]].chart.draw(
          lava.charts[parts[0]][parts[1]].data,
          lava.charts[parts[0]][parts[1]].options
        );
      }
    }, delay);
  };
};