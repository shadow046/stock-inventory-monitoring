var r = 1;
var y = 1;
var interval = null;
var requestno;
$(document).ready(function()
{
    var d = new Date();
    var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
    var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    $('#date').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);
    $('#sdate').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);

    var table =
    $('table.requestTable').DataTable({ 
        "dom": 'lrtip',
        "pageLength": 50,
        "language": {
            "emptyTable": " "
        },
        "order": [[ 6, 'asc'], [ 0, 'desc']],
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
        processing: true,
        serverSide: true,
        ajax: 'requests',
        columns: [
            { data: 'id', name:'id'},
            { data: 'created_at', name:'date', "width": "14%" },
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'branch', name:'branch',"width": "14%"},
            { data: 'type', name:'type', "width": "14%"},
            { data: 'status', name:'status', "width": "14%"},
            { data: 'ticket', name:'ticket', "width": "14%"}
        ]
    });
    $('#requestTable tbody').on('click', 'tr', function () {
        var trdata = table.row(this).data();
        var trsched = new Date(trdata.sched);
        requestno = trdata.request_no;
        $('.notes').hide();
        $('#head').text('STOCK REQUEST NO. '+trdata.request_no);
        $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        $('#date').val(trdata.created_at);
        $('#status').val(trdata.status);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('#reqbranch').val(trdata.branch_id);
        $('#requesttypes').val(trdata.type);
        if (trdata.type == "STOCK") {
            $('.ticketno').hide();
            $('#clientrows').hide();
        }else{
            $('.ticketno').show();
            $('#clientrows').show();
            $('#clients').val(trdata.client);
            $('#customers').val(trdata.customer);
            $('#tickets').val(trdata.ticket);
        }
        if (trdata.status == 'PENDING') {
            $('table.requestDetails').dataTable().fnDestroy();
            $('table.requestDetails').show();
            $('table.schedDetails').hide();
            $('table.schedDetails').dataTable().fnDestroy();
            $('.sched').hide();
            var penreq;
            Promise.all([pendingrequest()]).then(() => { 
                if (penreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    pendingreq = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'}
                        ]
                    });
                }else if (penreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    pendingreq = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'}
                        ]
                    });
                }
            });
            function pendingrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        penreq = data.data.length;
                    },
                });
            }
        }else if (trdata.status == 'RESOLVED') {
            $('.notes').show();
            $('#notes').text(trdata.remarks);
            $('table.requestDetails').dataTable().fnDestroy();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            $('.sched').show();
            $('table.schedDetails').DataTable({ 
                "dom": 'rt',
                "language": {
                    "emptyTable": " "
                },
                processing: true,
                serverSide: true,
                ajax: "/send/"+trdata.request_no,
                columnDefs: [
                    {"className": "dt-center", "targets": "_all"}
                ],
                columns: [
                    { data: 'items_id', name:'items_id'},
                    { data: 'item_name', name:'item_name'},
                    { data: 'quantity', name:'quantity'},
                    { data: 'serial', name:'serial'}
                ]
            });
        }else{
            if (trdata.status == 'UNRESOLVED') {
                $('.notes').show();
                $('#notes').text('The five days given to resolve the issue has lapsed [Since '+moment(trdata.updated_at).format("dddd, MMMM D, YYYY")+']. To resolve the issue a discussion with the warehouse team is recommended and input the remarks in the text field provided for the solution.');
            }
            $('table.requestDetails').dataTable().fnDestroy();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            $('.sched').show();
            $('table.schedDetails').DataTable({ 
                "dom": 'rt',
                "language": {
                    "emptyTable": " "
                },
                processing: true,
                serverSide: true,
                ajax: "/send/"+trdata.request_no,
                columnDefs: [
                    {"className": "dt-center", "targets": "_all"}
                ],
                columns: [
                    { data: 'items_id', name:'items_id'},
                    { data: 'item_name', name:'item_name'},
                    { data: 'quantity', name:'quantity'},
                    { data: 'serial', name:'serial'}
                ]
            });
        }   
        $('#requestModal').modal('show');

    });
});
$(document).on("click", ".schedDetails tr", function() {
    if ($('#status').val() == 'UNRESOLVED' && $('#level').val() == 'Manager') {
        $('#remarksModal').modal('show');
        $('#requestModal').modal('hide');
    }
});
$(document).on('click', '.close', function(){
    window.location.href = 'request';
});
$(document).on('click', '#remarks_btn', function(){
    $.ajax({
        url: 'resolved',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            requestno: requestno,
            remarks: $('#remarkstext').val()
        },
        success: function (data) {
            window.location.href = 'request';
        },
        error: function (data) {
            alert(data.responseText);
            return false;
        }
    });
});
