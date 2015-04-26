function MockChart() {
  this.chart = {
    prop : 1
  };

  this.data = {
    prop : 2
  };
}

describe('lava.js core functions', function() {

  describe('lava.get()', function() {

    beforeEach(function() {
      lava.charts = {
        "LineChart" : {
          "TestChart" : new MockChart()
        }
      };
    });

    it('should return a valid chart to the callback.', function() {
      lava.get('TestChart', function (chart) {
        expect(chart.prop).toEqual(1);
      });
    });

    it('should throw an error if the chart is not found.', function() {
      expect(function () { lava.get('Bee Population', function(){}); })
        .toThrow(new Error('[Lavacharts] Chart "Bee Population" was not found'));
    });

    it('should throw an error if a string chart label is not given.', function() {
      expect(function () { lava.get([], function(){}); })
        .toThrow(new Error('[Lavacharts] The syntax for lava.get must be (str ChartLabel, fn Callback)'));
    });

    it('should throw an error if a function callback is not given.', function() {
      expect(function () { lava.get('TestChart', {}); })
        .toThrow(new Error('[Lavacharts] The syntax for lava.get must be (str ChartLabel, fn Callback)'));
    });

  });

});
