jQuery(document).ready(function($) {
    $('tr[data-href]').children().not('.action-cell').on('click', function() {
        document.location = $(this).parent().data('href');
    });
});