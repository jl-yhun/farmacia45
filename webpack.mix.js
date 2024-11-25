let mix = require('laravel-mix');

mix.disableNotifications();
if (mix.inProduction()) { mix.version(); }
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js/app.js')
//    .sass('resources/assets/sass/app.scss', 'public/css');
let crud = 'resources/assets/js/old/crud.js';
mix.scripts([
    'resources/assets/js/old/es.js',
    'resources/assets/js/old/modal.js',
    'resources/assets/js/old/main.js'
], 'public/js/main.js')
    .styles([
        'resources/assets/css/old/font-awesome.min.css',
        'resources/assets/css/old/styles.css'
    ], 'public/css/main.css');

mix.scripts([
    crud
], 'public/js/categorias.js');

mix.scripts([
    crud
], 'public/js/gastos.js');

mix.scripts([
    crud
], 'public/js/motivos-garantias.js');

mix.scripts([
    'resources/assets/js/old/productos.js',
    crud,
], 'public/js/productos.js');

mix.scripts([
    'resources/assets/js/old/reporte-ventas.js'
], 'public/js/reporte-ventas.js')
    .styles([
        'resources/assets/css/old/reportes.css'
    ], 'public/css/reportes.css');

mix.scripts([
    'resources/assets/js/old/reporte-ventas-productos.js'
], 'public/js/reporte-ventas-productos.js')
    .styles([
        'resources/assets/css/old/reportes.css'
    ], 'public/css/reportes.css');

mix.scripts([
    'resources/assets/js/old/pdv.js'
], 'public/js/pdv.js')
    .styles([
        'resources/assets/css/old/pdv.css'
    ], 'public/css/pdv.css');

mix.scripts([
    crud,
    'resources/assets/js/old/garantias.js'
], 'public/js/garantias.js');

mix.scripts([
    crud,
    'resources/assets/js/old/perdidas.js'
], 'public/js/perdidas.js');

mix.scripts([
    crud,
    'resources/assets/js/old/usuarios.js'
], 'public/js/usuarios.js');

mix.scripts([
    'resources/assets/js/old/conteo.js'
], 'public/js/conteo.js');

mix.scripts([
    'resources/assets/js/old/estadisticas.js',
    'resources/assets/js/chart.js',
], 'public/js/estadisticas.js').styles([
    'resources/assets/css/old/estadisticas.css'
], 'public/css/estadisticas.css');

mix.js('resources/assets/js/apps/recargas-app.js',
    'public/js/recargas-app.js').vue();

mix.js('resources/assets/js/apps/servicios-app.js',
    'public/js/servicios-app.js').vue();

mix.js('resources/assets/js/apps/movimientos-app.js',
    'public/js/movimientos-app.js').vue();

mix.js('resources/assets/js/apps/transferencias-app.js',
    'public/js/transferencias-app.js').vue();

mix.js('resources/assets/js/apps/reinventario-app.js',
    'public/js/reinventario-app.js').vue();

mix.js('resources/assets/js/apps/reinventario-diff-app.js',
    'public/js/reinventario-diff-app.js').vue();

mix.js('resources/assets/js/apps/aperturas-caja-app.js',
    'public/js/aperturas-caja-app.js').vue();

mix.js('resources/assets/js/apps/ordenes-compra-faltantes-app.js',
    'public/js/ordenes-compra-faltantes-app.js').vue();

mix.js('resources/assets/js/apps/ordenes-compra-app.js',
    'public/js/ordenes-compra-app.js').vue();

mix.js('resources/assets/js/apps/similares-app.js',
    'public/js/similares-app.js').vue();

mix.js('resources/assets/js/apps/apartados-app.js',
    'public/js/apartados-app.js').vue();

mix.js('resources/assets/js/apps/productos-app.js',
    'public/js/productos-app.js').vue();

mix.js('resources/assets/js/apps/ventas-app.js',
    'public/js/ventas-app.js').vue();
