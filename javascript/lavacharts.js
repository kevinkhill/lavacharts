(function(){
  "use strict"

  this.lava = require('./lava.js');

  /**
   * Adding the resize event listener for redrawing charts.
   */
  this.addEventListener("resize", this.lava.redrawCharts);

  /**
   * Initialize the lava.js module by downloading Google's jsapi
   */
  this.lava.loadJsapi();

}).apply(window);
