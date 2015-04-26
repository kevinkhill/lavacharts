var lava = lava || {
  get              : null,
  event            : null,
  loadData         : null,
  register         : null,
  getLavachart     : null,
  charts           : {},
  registeredCharts : []
};

lava.get = function (chartLabel, callback) {
  lava.getLavachart(chartLabel, function(lavachart) {
    callback(lavachart.chart);
  });
};

lava.loadData = function (chartLabel, dataTableJson, callback) {
  lava.getLavachart(chartLabel, function (lavachart) {
    lavachart.data = new google.visualization.DataTable(dataTableJson, '0.6');

    lavachart.chart.draw(lavachart.data, lavachart.options);

    callback(lavachart.chart);
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

  if (typeof chartLabel === 'string' && typeof callback === 'function') {
    chartTypes.some(function (e) {
      if (typeof lava.charts[e][chartLabel] !== 'undefined') {
        if (callback.length == 1) {
          callback(lava.charts[e][chartLabel]);
        }
      } else {
        throw new Error('[Lavacharts] Chart "' + chartLabel + '" was not found');
      }
    });
  } else {
    throw new Error('[Lavacharts] The arguments for lava.get must be (str chartLabel, func Callback)');
  }
};

/*
window.onload = function() {
  var timer, delay = 500;
*/
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
//};