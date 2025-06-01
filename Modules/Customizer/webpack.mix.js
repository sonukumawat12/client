const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');

if (mix.inProduction()) {
  mix.setPublicPath('public/assets/');
} else {
  mix.setPublicPath('../../public/assets/modules/customizer');
}

mix.options({
    processCssUrls: false,
    clearConsole: true,
    PurgeCss: true,
});

mix.js(__dirname + '/Resources/assets/js/app.js', 'js/customizer.js').vue();
mix.sass( __dirname + '/Resources/assets/sass/app.scss', 'css/customizer.css');

if (mix.inProduction()) {
    mix.version();
}