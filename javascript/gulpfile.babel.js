/* jshint node:true */

import gulp from 'gulp';
import args from 'yargs';
import bump from 'gulp-bump';
import replace from 'gulp-replace';
import compile from './gulp-functions/Compile';
import renderChart from './gulp-functions/Renderer';
import getChartTypes from './gulp-functions/GetChartTypes';
import { map as promiseMap } from 'bluebird';
import { map, head, chunk } from 'lodash';

gulp.task('default', ['dev']);

/**
 * Lava.js compilation tasks.
 *
 * The compile method accepts three boolean flags for the following signature:
 *   compile(prod, watch, sync)
 */
gulp.task('dev',   () => { compile(false, false, false) });
gulp.task('prod',  () => { compile(true,  false, false) });
gulp.task('watch', () => { compile(false, true, false)  });
gulp.task('sync',  () => { compile(false, true, true)   });

/**
 * Get all available chart types
 *
 * Syntax:
 *   gulp charts
 */
gulp.task('charts', done => {
    getChartTypes(chartTypes => {
        console.log(chartTypes.join(', '));

        done();
    });
});

/**
 * Render a specific chart.
 *
 * Specify the type as the php class name
 *
 * Syntax:
 *   gulp render -t [ AreaChart | LineChart | GeoChart | etc... ]
 */
gulp.task('render', done => {
    // let chartType = args.type.replace(/\b[a-z]/g, letter => {
    //     return letter.toUpperCase();
    // });
    let chartType = args.t;

    getChartTypes(chartTypes => {
        if (chartTypes.indexOf(chartType) === -1) {
            return done(chartType + ' is not a valid chart type.');
        }

        renderChart(args.type)
            .then(() => {
                done();
            })
            .catch(err => {
                console.log(err);
            });
    });
});

/**
 * Render all of the available charts.
 *
 * Syntax:
 *   gulp render:all
 */
gulp.task('renderAll', done => {
    getChartTypes(chartTypes => {
        promiseMap(chartTypes, chartType => {
            return renderChart(chartType);
        }, {concurrency: 3})
        .then(() => {
            done();
        })
        .catch(err => {
            console.log(err);
        });
    });
});

/**
 * Render all of the available charts.
 *
 * Syntax:
 *   gulp version -v 4.0.0
 */
// gulp.task('version', done => {
//     let version = args.v;
//     let minorVersion = version.slice(0, -2);
//
//     gulp.src('./package.json')
//         .pipe(bump({version:args.v}))
//         .pipe(gulp.dest('./'));
//
//     gulp.src(['./README.md', './.travis.yml'])
//         .pipe(replace(/(["=\/-])[0-9]+\.[0-9]+/g, '$1'+minorVersion))
//         .pipe(gulp.dest('./'));
//
//     done();
// });
