var gulp = require('gulp'),
      sh = require('sh'),
    bump = require('gulp-bump'),
  jshint = require('gulp-jshint'),
 replace = require('gulp-replace'),
    argv = require('yargs').array('browsers').argv,
   karma = require('karma').server;


gulp.task('serve', function (done) {
  sh('php ../../../artisan serve');
});

gulp.task('phpunit', function (done) {
  sh('./vendor/bin/phpunit -c configs/phpunit.xml');
});

gulp.task('karma', function (done) {
  var conf = {
    configFile: __dirname + '/configs/karma.conf.js'
  };

  if (typeof argv.browsers !== 'undefined') {
    conf.browsers = argv.browsers;
  }

  if (argv.dev) {
    conf.singleRun = false;
  }

  karma.start(conf, done);
});

gulp.task('check', function (done) {
  sh('./vendor/bin/phpcs -n --standard=PSR2 ./src ./tests');
});

gulp.task('fix', function (done) {
  sh('./vendor/bin/phpcbf -n --standard=PSR2 ./src ./tests');
});

gulp.task('lint', function (done) {
  gulp.src('./javascript/lava.js')
      .pipe(jshint());
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
