/* assets/app.js */
import './bootstrap.js'; 
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/profile.css';
import $ from 'jquery'; // Import jQuery
import 'bootstrap'; // Import Bootstrap JavaScript
global.$ = global.jQuery = $; // Make jQuery available globally
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');




