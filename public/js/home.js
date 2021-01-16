$(document).ready(function()
{
    var table =
    $('table.activityTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
                "emptyTable": " ",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> '
            },
        "pageLength": 25,
        "order": [ 0, 'desc' ],
        processing: true,
        serverSide: false,
        ajax: {
            url: 'activity',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
        columns: [
            { data: 'id', name:'id'},
            { data: 'date', name:'date'},
            { data: 'username', name:'username'},
            { data: 'fullname', name:'fullname'},
            { data: 'activity', name:'activity'}
        ]
    });


    $('#search-ic').on("click", function () { 
        for ( var i=0 ; i<=4 ; i++ ) {
            $('.fl-'+i).val('').change();
            table
            .columns(i).search( '' )
            .draw();
        }
        $('.tbsearch').toggle();
        
    });

    $('.filter-input').keyup(function() { 
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
});