/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/dashboard.css');
require('../css/fontawesome.min.css');
require('../css/login.css');
require('../css/styles.css');

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
