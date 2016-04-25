  var gulp = require('gulp'),
     gutil = require('gulp-util'),
      bump = require('gulp-bump'),
    jshint = require('gulp-jshint'),
   stylish = require('jshint-stylish'),
   replace = require('gulp-replace'),
      argv = require('yargs').array('browsers').argv,
        fs = require('fs'),
browserify = require('browserify'),
  watchify = require('watchify');

var pkg = require('./package.json');

gulp.task('jshint', function (done) {
    gulp.src('./javascript/src/**/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter(stylish));
});

gulp.task('bump', function (done) { //-v=1.2.3
    var version = argv.v;
    var minorVersion = version.slice(0, -2);

    gulp.src('./package.json')
        .pipe(bump({version:argv.v}))
        .pipe(gulp.dest('./'));

    gulp.src(['./README.md', './.travis.yml'])
        .pipe(replace(/("|=|\/|-)[0-9]+\.[0-9]+/g, '$1'+minorVersion))
        .pipe(gulp.dest('./'));
});

function compile(watch) {
    var bundler = watchify(browserify({
        debug: true,
        entries: [pkg.config.entry],
        cache: {},
        packageCache: {}
    }));

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
            .pipe(fs.createWriteStream('./javascript/dist/lava.js'));
    }

    if (watch) {
        bundler.on('update', function() {
            gutil.log(gutil.colors.green('-> bundling...'));

            rebundle();
        });
    }

    return rebundle();
}

function watch() {
    return compile(true);
}

gulp.task('build', function() { return compile() });
gulp.task('watch', function() { return watch() });

gulp.task('default', ['watch']);
