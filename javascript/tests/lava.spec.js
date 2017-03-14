/* jshint undef: true, unused: true */
/* globals sinon, jasmine, describe, it, expect, beforeEach */

function nope () {
    return 'nope';
}

function getTestChart () {
    return lava.createChart('LineChart', 'TestChart');
}

function getData () {
    return {
        "cols": [
            {"type": "string", "label": "Stuff"},
            {"type": "number", "label": "Age"}
        ],
        "rows": [
            {"c": [{"v": "things"}, {"v": 37}]},
            {"c": [{"v": "thengs"}, {"v": 72}]}
        ]
    };
}

function getOptions () {
    return {
        "title": "Company Finances",
        "legend": "none",
        "colorAxis": {"colors": ["black", "green"]}
    };
}

describe('lava#EventEmitter', function () {
    it('Should emit and listen to events', function () {
        lava.on('test', function (val) {
            expect(val).toBe('taco');
        });

        lava.emit('test', 'taco');
    })
});

describe('lava#ready()', function () {
    it('Should accept a function to use as a callback.', function () {
        var callback = sinon.spy(lava, 'ready');

        lava.ready(callback);
        lava.init();

        expect(callback).toHaveBeenCalledOnce();
    });

    it('Should throw an "InvalidCallback" error if passed a non-function.', function () {
        expect(function () {
            lava.ready('marbles')
        }).toThrowError(lava._errors.InvalidCallback);
    });
});

describe('lava#event()', function () {
    var event, chart;

    beforeEach(function () {
        event = jasmine.createSpy('Event');
        chart = jasmine.createSpy('Chart');
    });

    it('Should accept an event, chart and callback and return said callback with event and chart as args.', function () {
        function callback (event, chart) {
            expect(event.and.identity()).toEqual('Event');
            expect(chart.and.identity()).toEqual('Chart');
        };

        var lavaEvent = function () {
            return lava.event(event, chart, callback);
        };

        lavaEvent(event, chart);
    });

    it('Should throw an "InvalidCallback" error if passed a non-function for the last param.', function () {
        expect(function () {
            lava.event('marbles');
        }).toThrowError(lava._errors.InvalidCallback);
    });
});

describe('lava#storeChart()', function () {
    beforeEach(function () {
        lava._charts = [];
    });

    it('Should store a chart in the "lava._charts" array.', function () {
        lava.storeChart(getTestChart());

        expect(lava._charts[0] instanceof lava.Chart).toBeTruthy();
    })
});

describe('lava#getChart()', function () {
    beforeEach(function () {
        lava._charts = [];
        lava._charts.push(getTestChart());
    });

    it('Should return a valid chart to the callback when given a valid chart label.', function () {
        lava.getChart('TestChart', function (chart) {
            expect(chart instanceof lava.Chart).toBeTruthy();
        });
    });

    it('Should throw a "ChartNotFound" error if the chart is not found.', function () {
        expect(function () {
            lava.getChart('Bee Population', nope);
        }).toThrowError(lava._errors.ChartNotFound);
    });

    it('Should throw an "InvalidLabel" error if a string chart label is not given.', function () {
        expect(function () {
            lava.getChart(1234, nope);
        }).toThrowError(lava._errors.InvalidLabel);
    });

    it('Should throw an "InvalidCallback" error if a function callback is not given.', function () {
        expect(function () {
            lava.getChart('TestChart', {});
        }).toThrowError(lava._errors.InvalidCallback);
    });
});

describe('lava#loadData()', function () {
    var chart, data, formats;

    beforeEach(function () {
        chart = getTestChart();
        data = getData();
        formats = [{format:""},{format:""}];

        sinon.stub(chart, 'setData').withArgs(data);
        sinon.stub(chart, 'redraw');
        sinon.stub(chart, 'applyFormats').withArgs(formats);

        lava._charts = [];
        lava._charts.push(chart);
    });

    describe('Loading data into the chart from the DataTable->toJson() PHP method', function () {
        it('should work with no formats', function () {
            lava.loadData('TestChart', data, function (chart) {
                expect(chart.setData).toHaveBeenCalledOnce();
                expect(chart.setData).toHaveBeenCalledWithExactly(data);

                expect(chart.redraw).toHaveBeenCalledOnce();
                expect(chart.redraw).toHaveBeenCalledAfter(chart.setData);
            });
        });

        describe('and when the DataTable has formats', function () {
            var formatted;

            beforeEach(function () {
                formatted = {
                    data: data,
                    formats: formats
                };
            });

            it('should still load data, but from the ".data" property', function () {
                lava.loadData('TestChart', formatted, function (chart) {
                    expect(chart.setData).toHaveBeenCalledOnce();
                    expect(chart.setData).toHaveBeenCalledWithExactly(formatted.data);

                    expect(chart.redraw).toHaveBeenCalledOnce();
                    expect(chart.redraw).toHaveBeenCalledAfter(chart.setData);
                });
            });

            it('should apply the formats', function () {
                lava.loadData('TestChart', formatted, function (chart) {
                    expect(chart.setData).toHaveBeenCalledOnce();
                    expect(chart.setData).toHaveBeenCalledWithExactly(formatted.data);

                    expect(chart.applyFormats).toHaveBeenCalledOnce();
                    expect(chart.applyFormats).toHaveBeenCalledAfter(chart.setData);
                    expect(chart.applyFormats).toHaveBeenCalledWithExactly(formatted.formats);

                    expect(chart.redraw).toHaveBeenCalledOnce();
                    expect(chart.redraw).toHaveBeenCalledAfter(chart.setData);
                });
            });
        });
    });
});

describe('lava#loadOptions()', function () {
    var chart, options;

    beforeEach(function () {
        chart = getTestChart();
        options = getOptions();

        sinon.stub(chart, 'setOptions').withArgs(options);
        sinon.stub(chart, 'redraw');

        lava._charts = [];
        lava._charts.push(chart);
    });

    it('should load new options into the chart.', function () {
        lava.loadOptions('TestChart', options, function (chart) {
            expect(chart.setOptions).toHaveBeenCalledOnce();
            expect(chart.setOptions).toHaveBeenCalledWithExactly(options);

            expect(chart.redraw).toHaveBeenCalledOnce();
            expect(chart.redraw).toHaveBeenCalledAfter(chart.setOptions);
        });
    });
});

/*
describe('lava#redrawCharts()', function () {
    it('Should be called when the window is resized.', function () {
        var resize = sinon.spy(lava, 'redrawCharts');

        window.dispatchEvent(new Event('resize'));

        expect(resize).toHaveBeenCalledOnce();
    });
});
*/