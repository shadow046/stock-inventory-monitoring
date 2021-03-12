var table;
var mydate = '';
var disposed;
$(document).ready(function()
{
    
    $("#min-date").datepicker({
        "dateFormat": "yy/mm/dd",
        onSelect: function(dateStr) {
            var min = $(this).datepicker('getDate') || new Date(); // Selected date or today if none
            $('#max-date').datepicker('option', {minDate: min});
        },
        maxDate: '0',
    })
    $("#max-date").datepicker({
        "dateFormat": "yy/mm/dd",
        minDate: '+0',
        maxDate: '0',
    })

    table =
    $('table.disposedTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span> ',
            "emptyTable": "No data found!"
        },
        "order": [[ 0, "desc", ]],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'dispose',
        error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'mydate', name: 'mydate'},
            { data: 'date', name:'date'},
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ],
    });

    $('#search-ic').on("click", function () { 
        for ( var i=0 ; i<=5 ; i++ ) {
            
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
var startdate = '';
var enddate = '';
$('#max-date').on("change", function () { 
    console.log($('#max-date').val());
    if (!$('#min-date').val()) {
        $(this).val('');
        alert('select start Date first!');
        return false;
    }
    enddate = $('#max-date').val();
});
$('#min-date').on("change", function () { 
    startdate = $('#min-date').val();
    if (!$('#min-date').val()) {
        $(this).val('');
        alert('select start Date first!');
        return false;
    }
});

$(document).on("click", "#goBtn", function() {
    if (!$('#min-date').val() || !$('#max-date').val()) {
        alert('select Date first!');
        return false;
    }
    table
    .rows( function ( idx, data, node ) {
        if (data.mydate != startdate || data.mydate != enddate){
            return idx;
        };
    } )
    .remove()
    .draw();
});

$(document).on("click", ".approveBtn", function() {
    var returnid = $(this).attr('return_id');
    console.log(returnid);
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: returnid,
            status: 'approved'
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});
$(document).on("click", ".disposeBtn", function() {
    var returnid = $(this).attr('return_id');
    console.log(returnid);
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: returnid,
            status: 'dispose'
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});
$(document).on("click", ".returnBtn", function() {
    var returnid = $(this).attr('return_id');
    console.log(returnid);
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: returnid,
            status: 'return'
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});