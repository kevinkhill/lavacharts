"use strict";

const EventEmitter = require('events');
const util = require('util');

/**
 * lava.js
 *
 * Author:  Kevin Hill
 * Email:   kevinkhill@gmail.com
 * Github:  https://github.com/kevinkhill/lavacharts
 * License: MIT
 */
var lava = function() {
  EventEmitter.call(this);
  this._ = require('underscore');

  this._charts            = [];
  this._dashboards        = [];
  this._registeredCharts  = [];
  this._readyCallback     = function(){};
  this._renderDashboards  = null;
  this.errors = require('./lava.errors.js')
};

util.inherits(lava, EventEmitter);

module.exports = new lava();

/**
 * LavaChart object.
 *
 * @constructor
 */
lava.prototype.Chart = function (type, label) {
  this.type    = type;
  this.label   = label;
  this.data    = null;
  this.chart   = null;
  this.options = null;
  this.formats = [];
  this.element = null;
  this.render  = function(){};
  this.setData = function (data) {
    this.data = new google.visualization.DataTable(data, '0.6');
  };
  this.setElement = function (elemId) {
    this.element = document.getElementById(elemId);

    if (! this.element) {
      throw this.errors.ELEMENT_ID_NOT_FOUND(elemId);
    }
  };
  this.redraw = function() {
    this.chart.draw(this.data, this.options);
  };
  this.applyFormats = function (formatArr) {
    for(var a=0; a < formatArr.length; a++) {
      var formatJson = formatArr[a];
      var formatter = new google.visualization[formatJson.type](formatJson.config);

      formatter.format(this.data, formatJson.index);
    }
  };
};

/**
 * Dashboard object.
 *
 * @constructor
 */
lava.prototype.Dashboard = function() {
  this.render    = null;
  this.data      = null;
  this.bindings  = [];
  this.dashboard = null;
};

lava.prototype.DataTable = function (data) {
  return new window.google.visualization.DataTable(data);
};

lava.prototype.ready = function (callback) {
  if (typeof callback !== 'function') {
    throw this.errors.INVALID_CALLBACK(callback);
  }

  this._readyCallback = callback;
};

/**
 * Event wrapper for chart events.
 *
 *
 * Used internally when events are applied so the user event function has
 * access to the chart within the event callback.
 *
 * @param {object} event
 * @param {object} chart
 * @param {function} callback
 */
lava.prototype.event = function (event, chart, callback) {
  return callback(event, chart);
};

/**
 * Registers a chart as being on screen, accessible to redraws.
 */
/*
lava.prototype.registerChart = function(type, label) {
  this._registeredCharts.push(type + ':' + label);
};
*/

/**
 * Loads a new DataTable into the chart and redraws.
 *
 *
 * Used with an AJAX call to a PHP method returning DataTable->toJson(),
 * a chart can be dynamically update in page, without reloads.
 *
 * @param {string} chartLabel
 * @param {string} json
 * @param {function} callback
 */
lava.prototype.loadData = function (chartLabel, json, callback) {
  this.getChart(chartLabel, function (chart, LavaChart) {
    if (typeof json.data != 'undefined') {
      LavaChart.setData(json.data);
    } else {
      LavaChart.setData(json);
    }

    if (typeof json.formats != 'undefined') {
      LavaChart.applyFormats(json.formats);
    }

    LavaChart.redraw();

    if (typeof callback == 'function') {
      callback(LavaChart.chart);
    }
  });
};

/**
 * Retrieve a Dashboard from lava.js
 *
 * @param  {string}   label    Dashboard label.
 * @param  {Function} callback Callback function
 */
lava.prototype.getDashboard = function (label, callback) {
  if (typeof this._dashboards[label] === 'undefined') {
    throw this.errors.DASHBOARD_NOT_FOUND(label);
  }

  var lavaDashboard = this._dashboards[label];

  if (typeof callback !== 'function') {
    throw this.errors.INVALID_CALLBACK(callback);
  }

  callback(lavaDashboard.dashboard, lavaDashboard);
};

/**
 * Stores a chart within lava.js and registers it for redraws.
 *
 * @param chart Chart
 */
lava.prototype.storeAndRegisterChart = function (chart) {
  this._charts.push(chart);

  //this._registeredCharts.push(chart.type + ':' + chart.label);
};

/**
 * Returns the GoogleChart and the LavaChart objects
 *
 *
 * The GoogleChart object can be used to access any of the available methods such as
 * getImageURI() or getChartLayoutInterface().
 * See https://google-developers.appspot.com/chart/interactive/docs/gallery/linechart#methods
 * for some examples relative to LineCharts.
 *
 * The LavaChart object holds all the user defined properties such as data, options, formats,
 * the google chart object, and relative methods for internal use.
 *
 * Just to clarify:
 *  - The first returned callback value is a property of the LavaChart.
 *    It was add as a shortcut to avoid chart.chart to access google's methods of the chart.
 *
 *  - The second returned callback value is the LavaChart, which holds the GoogleChart and other
 *    important information. It was added to not restrict the user to only getting the GoogleChart
 *    returned, and as the second value because it is less useful / rarely accessed.
 *
 * @param  {string}   chartLabel
 * @param  {function} callback
 */
lava.prototype.getChart = function (chartLabel, callback) {
  if (typeof chartLabel != 'string') {
    throw this.errors.INVALID_LABEL(chartLabel);
  }

  if (typeof callback != 'function') {
    throw this.errors.INVALID_CALLBACK(callback);
  }

 // var chartTypes = Object.keys(this._charts);
  var LavaChart = this._.find(this._charts, this._.matches({label:chartLabel}), this);

console.log(LavaChart);

  if (! LavaChart) {
    throw this.errors.CHART_NOT_FOUND(chartLabel);
  } else {
    callback(null, LavaChart);
  }
};

/**
 * Get the charts array and pass into the callback
 *
 * @param callback function
 */
lava.prototype.getCharts = function (callback) {
  if (typeof callback != 'function') {
    throw this.errors.INVALID_CALLBACK(callback);
  }

  callback(this._charts);
};

/**
 * Redraws all of the registered charts on screen.
 *
 *
 * This method is attached to the window resize event with a 300ms debounce
 * to make the charts responsive to the browser resizing.
 */
lava.prototype.redrawCharts = function() {
  var timer, delay = 300;

  clearTimeout(timer);

  timer = setTimeout(function() {
    for(var c = 0; c < this._registeredCharts.length; c++) {
      var parts = this._registeredCharts[c].split(':');

      this._charts[parts[0]][parts[1]].redraw();
    }
  }.bind(this), delay);
};

lava.prototype.loadJsapi = function() {
  var s = document.createElement('script');

  s.type = 'text/javascript';
  s.src = '//www.google.com/jsapi';
  s.onload = s.onreadystatechange = function (event) {
    event = event || window.event;

    if (event.type === "load" || (/loaded|complete/.test(this.readyState))) {
      this.onload = this.onreadystatechange = null;

      this.emit('jsapi:ready', window.google);
    }
  }.bind(this);

  document.head.appendChild(s);
};
