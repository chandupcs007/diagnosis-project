// Import jQuery
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Bootstrap
import 'bootstrap';

// Import Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import SB Admin 2
import '../../node_modules/startbootstrap-sb-admin-2/js/sb-admin-2.min.js';

// Your custom JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});