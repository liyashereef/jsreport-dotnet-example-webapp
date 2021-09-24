const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/employeetimeoff.js')
    .sass( __dirname + '/Resources/assets/sass/app.scss', 'css/employeetimeoff.css');

if (mix.inProduction()) {
    mix.version();
}
