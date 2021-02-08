/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import '@popperjs/core'
import {Tooltip} from 'bootstrap'
import $ from 'jquery'

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

$(document).ready(function() {
$('.js-tooltip').each(function () {
    new Tooltip($(this).get(0), {
        container: 'body',
        boundary: 'window'
    });
})
});