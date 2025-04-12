import './bootstrap';
import Alpine from 'alpinejs';
import jQuery from 'jquery';
import 'datatables.net';
import 'datatables.net-bs4';

window.$ = window.jQuery = jQuery;
window.Alpine = Alpine;

Alpine.start();

// Initialize DataTables
$(document).ready(function() {
    $('.datatable').DataTable();
});
