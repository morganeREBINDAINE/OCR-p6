/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');
require('./homepage.js');
require('./single.js');
require('./forms.js');
require('./edit-form.js');
require('./create-form.js');
require('./user-form.js');

document.addEventListener("DOMContentLoaded", function (event) {
    var height = document.body.offsetHeight
    if (height < screen.height) {
        document.querySelector('footer').classList.add('stickybottom');
    }
}, false);