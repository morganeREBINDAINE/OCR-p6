/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

$('#full-width-img').css('height', window.innerHeight)

$(function(){
    $('.arrow-down').delay( 2000 ).fadeIn( 800 )
    setTimeout(function(){
        let started = false
        const addBounce = () => {
            if(started === false) {
                started = true
                $('.arrow-down').addClass('animated bounce')
                setTimeout(function(){
                    $('.arrow-down').removeClass('animated bounce')
                    started = false
                }, 1000)
            }
        }

        addBounce()
        $('.arrow-down').hover(function(){
            addBounce()
        })
    }, 4000)
})