let mix = require("laravel-mix");
let tailwindcss = require("tailwindcss");
let postcssImport = require('postcss-import');
require("laravel-mix-purgecss");

mix
  .js("resources/assets/js/app.js", "public/js")
  .postCss("resources/assets/css/main.css", "public/css", [
    postcssImport(),
    tailwindcss("./tailwind.js")
  ])
  .purgeCss()
  .extract(['vue', 'axios', 'lodash', 'date-fns']);

if (mix.inProduction()) {
  mix.version();
}