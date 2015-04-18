function onResize (c, t) {
  window.onresize = function() {
    clearTimeout(t);

    t = setTimeout(c, 100);
  };

  return c;
}

var lava = lava || {
  get              : null,
  event            : null,
  charts           : {},
  registeredCharts : []
};

lava.get = function (chartLabel, callback) {
  var chartTypes = Object.keys(lava.charts);

  if (typeof chartLabel === 'string') {
    if (Array.isArray(chartTypes)) {
      chartTypes.some(function (e) {
        if (typeof lava.charts[e][chartLabel] !== 'undefined') {
          callback(lava.charts[e][chartLabel].chart);
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

lava.loadData = function (chartLabel, dataTableJson, callback) {
  lava.get(chartLabel, function (chart) {
    var newDataTable = new google.visualization.DataTable(dataTableJson, '0.6');

    chart.draw(newDataTable, chart.options);

    callback(chart);
  });
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