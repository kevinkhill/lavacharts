function Chart() {
  this.data = 5;
}

describe('lava.js core functions', function() {

  describe('lava.get()', function() {

    beforeEach(function() {
      lava.charts = {
        LineChart: {
          TestChart: new Chart()
        }
      };
    });

    it('should return a valid chart to the callback', function() {
      lava.get('TestChart', function (chart) {
        console.log(chart);
        expect(chart.data).toEqual(5);
      });
    });

  });

});
