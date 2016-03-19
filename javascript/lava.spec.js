var noop = function(){};

var MockLineChart = function() {
  this.chart = {
    prop : 'thing'
  };
  this.options = {"title":"Weather in October"};
  this.data = {"cols":[{"type":"date","label":"Date","id":"col_1"},{"type":"number","label":"Max Temp","id":"col_2"},{"type":"number","label":"Mean Temp","id":"col_3"},{"type":"number","label":"Min Temp","id":"col_4"}],"rows":[{"c":[{"v":"Date(2014,9,1,0,0,0)"},{"v":65},{"v":59},{"v":74}]},{"c":[{"v":"Date(2014,9,2,0,0,0)"},{"v":75},{"v":54},{"v":85}]},{"c":[{"v":"Date(2014,9,3,0,0,0)"},{"v":75},{"v":82},{"v":67}]},{"c":[{"v":"Date(2014,9,4,0,0,0)"},{"v":83},{"v":84},{"v":73}]},{"c":[{"v":"Date(2014,9,5,0,0,0)"},{"v":76},{"v":88},{"v":83}]},{"c":[{"v":"Date(2014,9,6,0,0,0)"},{"v":82},{"v":82},{"v":89}]},{"c":[{"v":"Date(2014,9,7,0,0,0)"},{"v":80},{"v":62},{"v":89}]},{"c":[{"v":"Date(2014,9,8,0,0,0)"},{"v":54},{"v":81},{"v":61}]},{"c":[{"v":"Date(2014,9,9,0,0,0)"},{"v":64},{"v":64},{"v":76}]},{"c":[{"v":"Date(2014,9,10,0,0,0)"},{"v":73},{"v":52},{"v":82}]},{"c":[{"v":"Date(2014,9,11,0,0,0)"},{"v":87},{"v":68},{"v":51}]},{"c":[{"v":"Date(2014,9,12,0,0,0)"},{"v":71},{"v":53},{"v":56}]},{"c":[{"v":"Date(2014,9,13,0,0,0)"},{"v":65},{"v":78},{"v":88}]},{"c":[{"v":"Date(2014,9,14,0,0,0)"},{"v":82},{"v":70},{"v":81}]},{"c":[{"v":"Date(2014,9,15,0,0,0)"},{"v":64},{"v":56},{"v":78}]},{"c":[{"v":"Date(2014,9,16,0,0,0)"},{"v":57},{"v":88},{"v":70}]},{"c":[{"v":"Date(2014,9,17,0,0,0)"},{"v":55},{"v":78},{"v":83}]},{"c":[{"v":"Date(2014,9,18,0,0,0)"},{"v":54},{"v":83},{"v":73}]},{"c":[{"v":"Date(2014,9,19,0,0,0)"},{"v":65},{"v":57},{"v":87}]},{"c":[{"v":"Date(2014,9,20,0,0,0)"},{"v":51},{"v":80},{"v":90}]},{"c":[{"v":"Date(2014,9,21,0,0,0)"},{"v":84},{"v":77},{"v":68}]},{"c":[{"v":"Date(2014,9,22,0,0,0)"},{"v":85},{"v":57},{"v":71}]},{"c":[{"v":"Date(2014,9,23,0,0,0)"},{"v":50},{"v":73},{"v":59}]},{"c":[{"v":"Date(2014,9,24,0,0,0)"},{"v":89},{"v":64},{"v":79}]},{"c":[{"v":"Date(2014,9,25,0,0,0)"},{"v":79},{"v":79},{"v":85}]},{"c":[{"v":"Date(2014,9,26,0,0,0)"},{"v":67},{"v":86},{"v":83}]},{"c":[{"v":"Date(2014,9,27,0,0,0)"},{"v":88},{"v":51},{"v":71}]},{"c":[{"v":"Date(2014,9,28,0,0,0)"},{"v":81},{"v":56},{"v":63}]},{"c":[{"v":"Date(2014,9,29,0,0,0)"},{"v":63},{"v":71},{"v":71}]},{"c":[{"v":"Date(2014,9,30,0,0,0)"},{"v":60},{"v":73},{"v":61}]}]};
  this.setData = function (data) {
    var $this = lava.charts.LineChart["TestChart"];

    $this.data = data;
  };
  this.redraw = function(){};
}

describe('lava.js core functions', function() {

  describe('lava.getChart()', function() {

    beforeEach(function() {
      lava.charts = {
        "LineChart" : {
          "TestChart" : new MockLineChart()
        }
      };
    });

    it('should return a valid chart to the callback.', function() {
      lava.getChart('TestChart', function (gchart, lchart) {
        expect(gchart.prop).toEqual('thing');

        expect(lchart.options.title).toEqual('Weather in October');
        expect(lchart.data.cols[0].type).toEqual('date');
      });
    });

    it('should throw an error if the chart is not found.', function() {
      expect(function() {
        lava.getChart('Bee Population', noop);
      })
      .toThrow(new Error('[Lavacharts] Chart "Bee Population" was not found.'));
    });

    it('should throw an error if a string chart label is not given.', function() {
      expect(function() {
        lava.getChart(1234, noop);
      })
      .toThrow(new Error('[Lavacharts] number is not a valid chart label.'));
    });

    it('should throw an error if a function callback is not given.', function() {
      expect(function() {
        lava.getChart('TestChart', {});
      })
      .toThrow(new Error('[Lavacharts] object is not a valid callback.'));
    });

  }); //lava.getChart()

/*
  describe('lava.loadData()', function() {

    beforeEach(function() {
      lava.charts = {
        "LineChart" : {
          "TestChart" : new lava.Chart()
        }
      };
    });

    it('should load the json data into the chart.', function() {
      lava.loadData('TestChart', {d1:100,d2:200}, function (gchart, lchart) {
        expect(lchart.data.d1).toEqual(100);
        expect(lchart.data.d2).toEqual(200);
      });
    });

  }); //lava.loadData()
*/
}); //lava.js core
