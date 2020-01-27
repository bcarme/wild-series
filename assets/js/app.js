/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

require('bootstrap');
// eslint-disable-next-line import/no-extraneous-dependencies


$(document).ready(() => {
    $('[data-toggle="popover"]').popover();
});

// eslint-disable-next-line func-names
$('.counter-up').each(function () {
    $(this)
        .prop('Counter', 0)
        .animate({
            Counter: $(this)
                .text(),
        }, {
            duration: 4000,
            easing: 'swing',
            step(now) {
                $(this)
                    .text(Math.ceil(now));
            },
        });
});
