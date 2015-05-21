// http://travismaynard.com/writing/getting-started-with-gulp
// #npm install gulp gulp-concat gulp-rename gulp-minify-css gulp-uglify gulp-jshint --save-dev
//
// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var minifyCSS = require('gulp-minify-css');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');

// Lint Task
gulp.task('lint', function() {
    return gulp.src('public/js/components/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// CSS
gulp.task('styles', function() {
    return gulp.src('public/css/components/*.css')
        .pipe(concat('compiled.css'))
        .pipe(gulp.dest('public/css'))
        .pipe(rename('compiled.min.css'))
        .pipe(minifyCSS({keepBreaks:true}))
        .pipe(gulp.dest('public/css'));
});

// Order matters
var myJS = [
    "public/js/components/device.js",
    "public/js/components/dimmer.js",
    "public/js/components/relay.js",
    "public/js/components/battery.js",
    "public/js/components/thermostat.js",
    "public/js/components/shortcut.js",
    "public/js/components/app.js",
    "public/js/components/listeners.js",
    "public/js/components/idle.js"
];

// JS
gulp.task('scripts', function() {
    return gulp.src(myJS)
        .pipe(concat('compiled.js'))
        .pipe(gulp.dest('public/js/'))
        .pipe(rename('compiled.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/'));
});

// Default Task
gulp.task('default', ['lint','styles','scripts']);