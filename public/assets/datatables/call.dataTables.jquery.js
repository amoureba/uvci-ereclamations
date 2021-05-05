// Call the dataTables jQuery plugin
$(document).ready(function() {
    $('#dataTable').DataTable();
    $('.dataTables_length').addClass('bs-select');
});

$(document).ready(function() {
    $('#dataTableActivity').DataTable({
        "order": [[ 0, 'desc' ]]
    });
});

