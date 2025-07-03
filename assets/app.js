/* assets/app.js */
import './bootstrap.js'; 
/*
 * Welcome to your app's main JavaScript file, managed by Webpack Encore.
 * This file, and any CSS it imports, will be compiled and included on the page
 * via the `encore_entry_script_tags()` and `encore_entry_link_tags()` Twig functions.
 */
import './styles/app.css';
import './styles/profile.css';
import $ from 'jquery'; // Import jQuery
import 'bootstrap'; // Import Bootstrap JavaScript
global.$ = global.jQuery = $; // Make jQuery available globally
console.log('This log comes from assets/app.js - Webpack Encore is running!');




