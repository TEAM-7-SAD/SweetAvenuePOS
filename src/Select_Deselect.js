$(document).ready(function () {
    var table = $('#example').DataTable();

    // Click event handler for table rows
    $('#example tbody').on('click', 'tr', function () {
        // Toggle selected class
        $(this).toggleClass('selected');
        // Check if all rows are selected
        var allRowsSelected = $('#example tbody tr').length === $('#example tbody tr.selected').length;
        // Toggle deselectAll button visibility
        $('#deselectAll').toggle(allRowsSelected);
        $('#selectAll').toggle(!allRowsSelected);
        // Apply background color to selected rows
        if ($(this).hasClass('selected')) {
            $(this).css('background-color', '#FFF0E9');
        } else {
            $(this).css('background-color', ''); // Reset to default
        }
    });

    // Click event handler for select all button
    $('#selectAll').click(function () {
        $('#example tbody tr').addClass('selected').css('background-color', '#FFF0E9');
        $('#deselectAll').show();
        $('#selectAll').hide();
    });

    // Click event handler for deselect all button
    $('#deselectAll').click(function () {
        $('#example tbody tr').removeClass('selected').css('background-color', ''); // Reset to default
        $('#selectAll').show();
        $('#deselectAll').hide();
    });
});
