const _ = require('lodash');
const Nightmare = require('nightmare');

  var gulp = require('gulp'),
      glob = require('glob'),
     gutil = require('gulp-util'),
      bump = require('gulp-bump'),
    uglify = require('gulp-uglify'),
 streamify = require('gulp-streamify'),
    gulpif = require('gulp-if'),
   replace = require('gulp-replace'),
      argv = require('yargs').array('browsers').argv,
    source = require('vinyl-source-stream'),
browserify = require('browserify'),
  babelify = require('babelify'),
  stripify = require('stripify'),
     bSync = require('browser-sync').create(),
   connect = require('gulp-connect-php'),
  watchify = require('watchify'),
  notifier = require('node-notifier');

const serverPort = 5000;
const renderOutputDir = process.cwd()+'/renders/';

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

function phpServer(router, callback) {
    const base = '../tests/Examples/';

    connect.server({
        base: base,
        port: serverPort,
        ini: base + 'php.ini',
        router: base + router
    }, callback);
}

gulp.task('default', ['dev']);

// compile(prod, watch, sync)
gulp.task('dev',   function() { return compile(false, false, false) });
gulp.task('watch', function() { return compile(false, true, false)  });
gulp.task('sync',  function() { return compile(false, true, true)   });
gulp.task('prod',  function() { return compile(true,  false, false) });

gulp.task('charts', function() {
    getChartTypes(charts => {
        console.log(charts);
    });
});

gulp.task('render', done => {
    phpServer('renderer.php', () => {
        getChartTypes(chartTypes => {
            let renders = _.map(chartTypes, chartType => {
                const nightmare = Nightmare();

                gutil.log(gutil.colors.green('Rendering '+chartType));

                return nightmare
                    .viewport(800, 600)
                    .goto('http://localhost:'+serverPort+'/'+chartType)
                    .wait(3000)
                    .screenshot(renderOutputDir+chartType+'.png')
                    .end()
                    .catch(err => {
                        console.log(err);
                    });
            });

            Promise.all(renders).then(connect.closeServer);
        });
    });
});

gulp.task('bump', function (done) { //-v=1.2.3
    let version = argv.v;
    let minorVersion = version.slice(0, -2);

    gulp.src('./package.json')
        .pipe(bump({version:argv.v}))
        .pipe(gulp.dest('./'));

    gulp.src(['./README.md', './.travis.yml'])
        .pipe(replace(/("|=|\/|-)[0-9]+\.[0-9]+/g, '$1'+minorVersion))
        .pipe(gulp.dest('./'));
});
