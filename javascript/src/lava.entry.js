(function(){
  "use strict"

  this.lava = require('./lava.js');

  /**
   * Adding the resize event listener for redrawing charts.
   */
  this.addEventListener('resize', this.lava.redrawCharts);

  /**
   * Let's go!
   */
  this.lava.run();

}).apply(window);
