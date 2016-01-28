/**
 * jvent.min.js
 *
 * Author:   Guille Paz
 * Email:    guille87paz@gmail.com
 * Github:   https://github.com/pazguille/jvent
 * Homepage: http://pazguille.me
 * License:  MIT
 */
!function(t){"use strict";function e(){}e.prototype.on=function(t,e){return this._collection=this._collection||{},this._collection[t]=this._collection[t]||[],this._collection[t].push(e),this},e.prototype.once=function(t,e){function i(){o.off(t,i),e.apply(this,arguments)}var o=this;return i.listener=e,this.on(t,i),this},e.prototype.off=function(t,e){var i=this._collection[t],o=0;if(void 0!==i)for(o;o<i.length;o+=1)if(i[o]===e||i[o].listener===e){i.splice(o,1);break}return 0===i.length&&this.removeAllListeners(t),this},e.prototype.removeAllListeners=function(t){return this._collection=this._collection||{},delete this._collection[t],this},e.prototype.listeners=function(t){return this._collection=this._collection||{},this._collection[t]},e.prototype.emit=function(){if(void 0===this._collection)return this;var t,e=[].slice.call(arguments,0),i=e.shift(),o=this._collection[i],n=0;if(o)for(o=o.slice(0),t=o.length,n;t>n;n+=1)o[n].apply(this,e);return this},"function"==typeof t.define&&void 0!==t.define.amd?t.define("Jvent",[],function(){return e}):"undefined"!=typeof module&&void 0!==module.exports?module.exports=e:t.Jvent=e}(this);

/**
 * lava.js
 *
 * Author:  Kevin Hill
 * Email:   kevinkhill@gmail.com
 * Github:  https://github.com/kevinkhill/lavacharts
 * License: MIT
 */
var lava = lava || {};

(function() {
  "use strict";

  this.charts            = [];
  this.dashboards        = [];
  this.registeredCharts  = [];
  this.readyCallback     = null;
  this.renderDashboards  = null;

  this.events = new Jvent();

  //var registeredActions = [];

  /**
   * LavaChart object.
   *
   * @constructor
   */
  this.Chart = function() {
    var self = this;
    this.data    = null;
    this.chart   = null;
    this.options = null;
    this.formats = [];
    this.render  = function(){};
    this.setData = function(){};
    this.redraw  = function(){};
    this.applyFormats = function (formatArr) {
      for(var a=0; a < formatArr.length; a++) {
        var formatJson = formatArr[a];
        var formatter = new google.visualization[formatJson.type](formatJson.config);
        formatter.format(self.data, formatJson.index);
      }
    };
  };

  /**
   * Dashboard object.
   *
   * @constructor
   */
  this.Dashboard = function() {
    this.render    = null;
    this.data      = null;
    this.bindings  = [];
    this.dashboard = null;
    this.callbacks = [];
  };

  this.Callback = function (label, func) {
    this.label = label;
    this.func  = func;
  };

  this.ready = function (callback) {
    if (typeof callback !== 'function') {
      throw new Error('[Lavacharts] ' + typeof callback + ' is not a valid callback.');
    } else {
      var callback = function(){};
    }

    lava.readyCallback = callback;
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
  this.event = function (event, chart, callback) {
    return callback(event, chart);
  };

  /**
   * Registers a chart as being on screen, accessible to redraws.
   */
  this.registerChart = function(type, label) {
    this.registeredCharts.push(type + ':' + label);
  };

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
  this.loadData = function (chartLabel, json, callback) {
    lava.getChart(chartLabel, function (chart, LavaChart) {
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

  this.getDashboard = function (label, callback) {
    if (typeof lava.dashboards[label] === 'undefined') {
      throw new Error('[Lavacharts] Dashboard "' + label + '" was not found.');
    }

    var lavaDashboard = lava.dashboards[label];

    if (typeof callback !== 'function') {
      throw new Error('[Lavacharts] ' + typeof callback + ' is not a valid callback.');
    }

    callback(lavaDashboard.dashboard, lavaDashboard);
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
  this.getChart = function (chartLabel, callback) {
    if (typeof chartLabel != 'string') {
      throw new Error('[Lavacharts] ' + typeof chartLabel + ' is not a valid chart label.');
    }

    if (typeof callback != 'function') {
      throw new Error('[Lavacharts] ' + typeof callback + ' is not a valid callback.');
    }

    var chartTypes = Object.keys(lava.charts);
    var LavaChart;

    var search = chartTypes.some(function (type) {
      if (typeof lava.charts[type][chartLabel] !== 'undefined') {
        LavaChart = lava.charts[type][chartLabel];

        return true;
      } else {
        return false;
      }
    });

    if (search === false) {
      throw new Error('[Lavacharts] Chart "' + chartLabel + '" was not found.');
    }

    callback(LavaChart.chart, LavaChart);
  };

  /**
   * Redraws all of the registered charts on screen.
   *
   *
   * This method is attached to the window resize event with a 300ms debounce
   * to make the charts responsive to the browser resizing.
   */
  this.redrawCharts = function() {
    var timer, delay = 300;

    clearTimeout(timer);

    timer = setTimeout(function() {
      for(var c = 0; c < lava.registeredCharts.length; c++) {
        var parts = lava.registeredCharts[c].split(':');

        lava.charts[parts[0]][parts[1]].redraw();
      }
    }, delay);
  };

  this.run = function () {
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.src = '//www.google.com/jsapi';
    s.onload = s.onreadystatechange = function (event) {
      event = event || window.event;

      if (event.type === "load" || (/loaded|complete/.test(this.readyState))) {
        this.onload = this.onreadystatechange = null;

        lava.events.emit('jsapi:ready', window.google);
      }
    };

    document.head.appendChild(s);
  };

}).apply(lava);

/**
 * Adding the resize event listener for redrawing charts.
 */
window.addEventListener("resize", window.lava.redrawCharts);

lava.run();
