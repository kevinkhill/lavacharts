var gulp = require('gulp'),
      sh = require('sh'),
    argv = require('yargs').argv,
    bump = require('gulp-bump'),
  uglify = require('gulp-uglify'),
  rename = require('gulp-rename'),
 replace = require('gulp-replace');


gulp.task('test', function (cb) {
  sh('./vendor/bin/phpunit -c configs/phpunit.xml');
});

gulp.task('serve', function (cb) {
  sh('php ../../../artisan serve');
});

gulp.task('check', function (cb) {
  sh('./vendor/bin/phpcs -n --standard=PSR2 ./src ./tests');
});

gulp.task('fix', function (cb) {
  sh('./vendor/bin/phpcbf -n --standard=PSR2 --ignore=' + scriptSrc + ' ./src ./tests');
});

gulp.task('uglify', function (cb) {
  gulp.src('./src/Javascript/src/lava.js')
      .pipe(uglify())
      .pipe(rename({suffix:'.min'}))
      .pipe(gulp.dest('./src/Javascript/dist'));
});

gulp.task('bump', function (cb) {
  var version = argv.v;
  var minorVersion = version.slice(0, -2);

  gulp.src('./package.json')
      .pipe(bump({version:argv.v}))
      .pipe(gulp.dest('./'));

  gulp.src(['./README.md', './.travis.yml'])
      .pipe(replace(/("|=|\/|-)[0-9]+\.[0-9]+/g, '$1'+minorVersion))
      .pipe(gulp.dest('./'));
});
