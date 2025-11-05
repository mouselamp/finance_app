const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Tailwind CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// Production optimization
if (mix.inProduction()) {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true,
                },
            },
        },
    });
}

// Compile Tailwind CSS
mix.postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'),
    require('autoprefixer'),
])

// Bundle JavaScript
mix.js('resources/js/app.js', 'public/js')
    .sourceMaps();

// Version for cache busting
mix.version();
