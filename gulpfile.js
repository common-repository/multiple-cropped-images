// Require Gulp Modules
const gulp    = require('gulp');
const plugins = require('gulp-load-plugins')();

// Set the Default Task
gulp.task('default', ['watch']);

// Watch Task
gulp.task('watch', [], () =>
{
	gulp.watch(['src/assets/js/**/*.js', '!src/assets/js/**/*.min.js'], ['js']);
	gulp.watch('src/assets/scss/**/*.scss', ['scss']);
	gulp.watch('src/assets/ts/**/*.ts', ['ts']);
});

// JS Task
gulp.task('js', [], () =>
{
	gulp.src(['src/assets/js/**/*.js', '!src/assets/js/**/*.min.js'])
	.pipe(plugins.plumber())
	.pipe(plugins.babel({presets: ['es2015']}))
	.pipe(plugins.uglify())
	.pipe(plugins.rename({suffix: '.min'}))
	.pipe(plugins.chmod(0o777))
	.pipe(gulp.dest('src/assets/js'));
});

// SCSS Task
gulp.task('scss', [], () =>
{
	gulp.src('src/assets/scss/**/*.scss')
	.pipe(plugins.plumber())
	.pipe(plugins.sourcemaps.init())
	.pipe(plugins.sass())
	.pipe(plugins.cleanCss())
	.pipe(plugins.autoprefixer())
	.pipe(plugins.rename({suffix: '.min'}))
	.pipe(plugins.sourcemaps.write('.'))
	.pipe(plugins.chmod(0o777))
	.pipe(gulp.dest('src/assets/css'));
});

// TS Task
gulp.task('ts', [], () =>
{
	gulp.src('src/assets/ts/**/*.ts')
	.pipe(plugins.plumber())
	.pipe(plugins.typescript({target: 'ES6'}))
	.pipe(plugins.babel({presets: ['es2015']}))
	.pipe(plugins.uglify())
	.pipe(plugins.rename({suffix: '.min'}))
	.pipe(plugins.chmod(0o777))
	.pipe(gulp.dest('src/assets/ts'));
});


// Dist Task
gulp.task('dist', ['ts', 'js', 'scss'], function()
{
	gulp.src(
	[
		'src/**/*',
		'!src/assets/ts', '!src/assets/ts/**/*',
		'!src/assets/scss', '!src/assets/scss/**/*'
	])
	.pipe(plugins.chmod(0o777))
	.pipe(gulp.dest('dist'));
});