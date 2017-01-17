var gulp = require('gulp');
var sass = require('gulp-sass');
var livereload = require('gulp-livereload');
var autoprefixer = require('gulp-autoprefixer');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var util = require('gulp-util');

gulp.task('assets:responsive', function () {
    var responsive = require('gulp-responsive');

    return gulp.src('assets/imgs/notes.jpg')
        .pipe(responsive({
            'notes.jpg': [
                {
                    rename: { suffix: '-512' },
                    width: 512
                },
                {
                    rename: { suffix: '-1024' },
                    width: 1024
                },
                {
                    rename: { suffix: '-1920' },
                    width: 1920
                },
                {
                    rename: { suffix: '-large' }
                }
            ]
        })).pipe(gulp.dest('assets/imgs'));
});

gulp.task('sass', function(){
    return gulp.src('assets/scss/*.scss')
        .pipe(sass()).on('error', swallowError)
        .pipe(autoprefixer({
            browsers: ['last 2 versions']
        })).on('error', swallowError)
        .pipe(gulp.dest('assets/css'))
        .pipe(livereload()).on('error', swallowError);
    s});

gulp.task('js', function() {
    return gulp.src(['assets/js/**/*.js', '!assets/js/**/*.min.js'])
        .pipe(concat('main.min.js')).on('error', swallowError)
        .pipe(uglify()).on('error', swallowError)
        .pipe(gulp.dest('js'))
        .pipe(livereload()).on('error', swallowError);
});


gulp.task('watch', function(){
    livereload.listen();

    gulp.watch('assets/scss/**/*.scss', ['sass']);
    gulp.watch('assets/js/**/*.js', ['js']);
});

function swallowError(error) {
    console.error(error.toString());
    this.emit('end');
}
