/* jshint node:true */

const _ = require('lodash');
const Nightmare = require('nightmare');
const PhpServer = require('gulp-connect-php');
const PortFinder = require('portfinder');

  var gulp = require('gulp'),
      glob = require('glob'),
     gutil = require('gulp-util'),
      bump = require('gulp-bump'),
      path = require('path'),
    uglify = require('gulp-uglify'),
 streamify = require('gulp-streamify'),
    gulpif = require('gulp-if'),
   replace = require('gulp-replace'),
      args = require('yargs').array('browsers').argv,
    source = require('vinyl-source-stream'),
browserify = require('browserify'),
  babelify = require('babelify'),
  stripify = require('stripify'),
     bSync = require('browser-sync').create(),
  watchify = require('watchify'),
  notifier = require('node-notifier');

function compile(prod, watch, sync) {
    let bundler = browserify({
        debug: true,
        entries: ['./src/lava.entry.es6'],
        cache: {},
        packageCache: {}
    })
    .transform(babelify, { presets: ['es2015'] });

    if (watch) {
        bundler = watchify(bundler);

        if (sync) {
            bSync.init({
                proxy: "localhost:8000"
            });
        }
    }

    if (prod) {
        bundler.transform('stripify');
    }

    function rebundle() {
        return bundler.bundle()
            .on('error', function(err){
                if (err instanceof SyntaxError) {
                    gutil.log(gutil.colors.red('Syntax Error'));
                    console.log(err.message);
                    // console.log(err.filename+":"+err.loc.line);
                    console.log(err.codeFrame);
                } else {
                    gutil.log(gutil.colors.red('Error'), err.message);
                }
                this.emit('end');
            })
            .pipe(source('lava.js'))
            .pipe(gulpif(prod, streamify(uglify())))
            .pipe(gulp.dest('dist'));
    }

    if (watch) {
        bundler.on('update', function() {
            const msg = 'lava.js re-bundling...';

            gutil.log(gutil.colors.green(msg));

            notifier.notify({
                title: 'Browserify',
                message:msg
            });

            rebundle();
        });

        bundler.on('log', function (msg) {
            gutil.log(gutil.colors.green(msg));

            if (sync) {
                bSync.reload();
            }
        });
    }

    return rebundle();
}

function getChartTypes(callback) {
    glob('*.php', {
        cwd: '../src/Charts/',
        nomount: true
    }, (err, chartTypes) => {
        _.pullAll(chartTypes, [
            'Chart.php',
            'ChartBuilder.php',
            'ChartFactory.php',
        ]);

        callback(_.map(chartTypes, chartType => {
            return chartType.slice(0, -4);
        }));
    });
}

function getNightmare(timeout) {
    return new Nightmare({
        gotoTimeout: timeout,
        waitTimeout: timeout,
        loadTimeout: timeout,
        executionTimeout: timeout
    });
}

function createPhpServer(port) {
    const base = path.resolve(process.cwd(), '../tests/Examples');
    const server = new PhpServer();

    return new Promise(resolve => {
        server.server({
            base: base,
            port: port,
            ini: base + '/php.ini',
            router: base + '/renderer.php'
        });

        resolve(server);
    });
}

function getPhpServer() {
    return PortFinder.getPortPromise()
        .then(createPhpServer)
        .catch(err => {
            console.log(err);
        });
}

function renderChart(chartType) {
    return getPhpServer()
        .then(server => {
            let chartUrl = 'http://localhost:' + server.port + '/' + chartType;
            let renderDir = path.resolve(process.cwd(), 'renders');
            let chartImg = renderDir + '/' + chartType + '.png';

            console.log('Nightmare opening ' + chartUrl);

            return getNightmare(5000)
                .viewport(800, 600)
                .goto(chartUrl)
                .wait(3000)
                .screenshot(chartImg)
                .end(() => {
                    console.log('Saved screenshot to ' + chartImg);

                    server.closeServer();
                })
                // .then();
        });

}

gulp.task('default', ['dev']);

/**
 * Lava.js compilation tasks.
 *
 * The compile method accepts three boolean flags for the following signature:
 *   compile(prod, watch, sync)
 */
gulp.task('dev',   () => { compile(false, false, false) });
gulp.task('watch', () => { compile(false, true, false)  });
gulp.task('sync',  () => { compile(false, true, true)   });
gulp.task('prod',  () => { compile(true,  false, false) });

/**
 *
 */
gulp.task('charts', (done) => {
    getChartTypes(chartTypes => {
        console.log(chartTypes);

        done();
    });
});

/**
 * Render a specific chart.
 *
 * Specify the type as the php classname
 *
 * Syntax:
 *   gulp render --type [ AreaChart | LineChart | GeoChart | etc... ]
 */
gulp.task('render', done => {
    // let chartType = args.type.replace(/\b[a-z]/g, letter => {
    //     return letter.toUpperCase();
    // });
    let chartType = args.type;

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
        Promise.all(_.map(chartTypes, renderChart))
            .then(() => {
                done();
            })
            .catch(err => {
                console.log(err);
            });
    });
});

gulp.task('bump', done => { //-v=1.2.3
    let version = args.v;
    let minorVersion = version.slice(0, -2);

    gulp.src('./package.json')
        .pipe(bump({version:args.v}))
        .pipe(gulp.dest('./'));

    gulp.src(['./README.md', './.travis.yml'])
        .pipe(replace(/(["=\/-])[0-9]+\.[0-9]+/g, '$1'+minorVersion))
        .pipe(gulp.dest('./'));

    done();
});
