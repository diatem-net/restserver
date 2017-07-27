// Gulp
const gulp = require('gulp');

// Other plugins
const apidoc = require('gulp-apidoc');
const argv   = require('yargs').argv;

var defaultVersion = 1;

// Watch
gulp.task('watch', function() {
    var version = (argv.version !== undefined) ? argv.version : defaultVersion;
    gulp.watch('./v' + version + '/**/*.php', function(event) {
        gendoc(version);
    });
});

// Documentation
gulp.task('doc', function() {
    var version = (argv.version !== undefined) ? argv.version : defaultVersion;
    return gendoc(version);
});

function gendoc(version) {
    return apidoc({
        src:  'v' + version + '/',
        dest: 'doc/v' + version + '/',
        includeFilters: [ ".*\\.php$" ]
    }, function() {});
}