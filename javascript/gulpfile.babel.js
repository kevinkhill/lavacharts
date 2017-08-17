/* jshint node:true */

import gulp from 'gulp';
import yargs from 'yargs';
import bump from 'gulp-bump';
import replace from 'gulp-replace';
import babel from 'gulp-babel';
import source from 'vinyl-source-stream';
import browserify from 'browserify';
import compile from './gulp-functions/Compile';
import renderChart from './gulp-functions/Renderer';
import getChartTypes from './gulp-functions/GetChartTypes';
import { cpus } from 'os'
import { map } from 'bluebird';
import { log } from 'gulp-util';
import { readFileSync } from 'fs';

const pkg = JSON.parse(readFileSync('./package.json'));

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

gulp.task('lavajs', () =>
    gulp.src('src/lava/lava.es6')
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(tap(file => {

            log('bundling ' + file.path);

            // replace file contents with browserify's bundle stream
            file.contents = browserify(file.path, {debug: true})

                .transform('versionify')
                .bundle();

        }))
        // .pipe(source('lava.js'))
        // .pipe(gulpif(prod, streamify(uglify())))
        .pipe(replace('__VERSION__', pkg.version))
        .pipe(gulp.dest('dist'))
);

/**
 * Render a specific chart.
 *
 * Specify the type as the php class name
 *
 * Syntax:
 *   gulp render --type [ AreaChart | LineChart | GeoChart | etc... ]
 */
gulp.task('render', done => {
    const chartTypes = getChartTypes();
    const args = yargs
        .fail(msg => {
            throw new Error(msg);
        })
        .alias('t', 'type')
        .describe('t', 'choose the type of chart to render')
        .choices('t', chartTypes)
        .wrap(70)
        .help('help')
        .argv;

    renderChart(args.t)
        .then(() => {
            done();
        })
        .catch(err => {
            console.log(err);
        });
});

/**
 * Render all of the available charts.
 *
 * The renders will be ran in batches equal to the number of processors.
 *
 * Syntax:
 *   gulp renderAll
 */
gulp.task('renderAll', done => {
    let batchSize = cpus().length;

    console.log('Rendering charts in batches of '+batchSize);

    map(getChartTypes(), chartType => {
        return renderChart(chartType);
    }, {concurrency: batchSize})
    .then(() => {
        done();
    })
    .catch(err => {
        console.log(err);
    });
});

/**
 * Get all available chart types
 *
 * Syntax:
 *   gulp charts
 */
gulp.task('charts', done => {
    console.log('Available charts for rendering:');
    console.log(getChartTypes().join(', '));
    done();
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
