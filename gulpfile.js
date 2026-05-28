// 本番サーバーにはあげない

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssSorter = require('css-declaration-sorter');
const gcmq = require('gulp-group-css-media-queries');
const browserSync = require('browser-sync').create();
const cleanCss = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');

// --- パス設定 ---
const paths = {
  sass: './src/assets/sass/**/*.scss',
  js: './assets/js/**/*.js',
  jsWatch: [
    './assets/js/**/*.js',
    '!./assets/js/**/*.min.js'
  ],
  img: './src/assets/img/**/*',
  cssDest: './assets/css',
  jsDest: './assets/js',
  imgDest: './assets/img'
};

// --- Sassコンパイル ---
function compileSass() {
  return gulp.src(paths.sass)
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss([autoprefixer(), cssSorter()]))
    .pipe(gcmq())
    .pipe(gulp.dest(paths.cssDest))
    .pipe(browserSync.stream())
    .pipe(cleanCss())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.cssDest));
}

// --- JS圧縮 ---
function minJs() {
  return gulp.src(paths.jsWatch)
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(paths.jsDest));
}

// --- 画像コピー ---
function copyImage() {
  return gulp.src(paths.img)
    .pipe(gulp.dest(paths.imgDest));
}

function browserInit(done) {
  browserSync.init({
    proxy: 'http://toyo-portfolio.local',
    open: false
  });
  done();
}

function browserReload(done) {
  browserSync.reload();
  done();
}

// --- ファイル監視 ---
function watchFiles() {
  gulp.watch(paths.sass, compileSass);
  gulp.watch(paths.jsWatch, gulp.series(minJs, browserReload));
  gulp.watch('./**/*.php', browserReload);
}

// --- タスク定義 ---
exports.compileSass = compileSass;
exports.minJs = minJs;
exports.copyImage = copyImage;
exports.watch = watchFiles;
exports.browserInit = browserInit;
exports.dev = gulp.parallel(browserInit, watchFiles);
exports.build = gulp.parallel(compileSass, minJs, copyImage);
exports.default = exports.dev;