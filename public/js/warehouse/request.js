var r = 1;
var y = 1;
var c = 1;
var w = 0;
var uomcount = 0;
var uomarray = new Array();
var bID;
var sub = 0;
var save = 0;
var pcount = 0;
var requestdetails;
var stat = "notok";
var pending = 0;
var check = false;
var requestgo;
var reqnumber;

$(document).ready(function()
{
    $("#datesched").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, '31'),
        minDate: 0
    });
    $("#resched").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, '31'),
        minDate: 0
    });
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
            "emptyTable": "No stock request found!"
        },
        "fnRowCallback": function(nRow, aData) {
            if ( aData.status == "UNRESOLVED" || aData.status == "INCOMPLETE") {        
                $('td', nRow).css('background-color', 'Red');
            }
        },
        "order": [[ 5, 'desc'], [ 0, 'desc']],
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'requests',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
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
        bID = trdata.branch_id
        reqnumber = trdata.request_no;
        $('.notes').hide();
        $('#head').text('STOCK REQUEST NO. '+trdata.request_no);
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
        if (trdata.status == 'SCHEDULED') {
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            $('#save_Btn').hide();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if (trdata.status == 'RESCHEDULED') {
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            $('#save_Btn').hide();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if(trdata.status == 'PENDING'){
            $('#prcBtn').show();
            $('.sched').hide();
            $('#sched').val('');
            $('#printBtn').hide();
            $('#save_Btn').show();
        }else if(trdata.status == 'UNRESOLVED'){
            $('#printBtn').show();
            $('#printBtn').val('PRINT');
            $('.sched').hide();
            $('.notes').show();
            $('#notes').text('Please be informed that the current status is now UNRESOLVED after the five days given to resolve the issue. Kindly contact the manager to resolve the issue.');
            $('table.requestDetails').hide();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.schedDetails').show();
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
            //$('#unresolveBtn').hide();
            $('#prcBtn').hide();
        }else if(trdata.status == 'PARTIAL'){
            $('#prcBtn').show();
            $('.sched').hide();
            $('#sched').val('');
            $('#printBtn').hide();
            $('#save_Btn').show();
        }else if(trdata.status == 'INCOMPLETE'){
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            var trsched = new Date(trdata.sched);
            $('.notes').show();
            if (trdata.left != 0) {
                trdata.left++;
                if (trdata.left > 1) {
                    var withs = 'days';
                }else if(trdata.left == 1){
                    trdata.hour == trdata.hour-24;
                    var withs = 'day and '+toWords(trdata.hour)+'('+trdata.hour+') hours';
                    if (trdata.hour == 1) {
                        var withs = 'day and '+toWords(trdata.hour)+'('+trdata.hour+') hour';
                    }
                }
                $('#notes').text('Please be informed that you only have '+toWords(trdata.left)+'('+trdata.left+') '+withs+' to resolve this issue. Once the '+toWords(trdata.left)+'('+trdata.left+') '+withs+' given has elapsed, the status of this issue will be automatically converted to UNRESOLVE.');
            }else{
                if (trdata.left == 0) {
                    if (trdata.hour != 0) {
                        var withs = 'hours';
                        trdata.left = trdata.hour;
                        if (trdata.hour == 1) {
                            trdata.minute = trdata.minute-60;
                            var withs = 'hour and '+toWords(trdata.minute)+'('+trdata.minute+') minutes';
                            if (trdata.minute == 1) {
                                var withs = 'hour and '+toWords(trdata.minute)+'('+trdata.minute+') minute';
                            }
                        }
                    }else{
                        var withs = 'minutes';
                        trdata.left = trdata.minute;
                    }
                }
                $('#notes').text('Please be informed that you only have '+toWords(trdata.left)+'('+trdata.left+') '+withs+' to resolve this issue. Once the '+toWords(trdata.left)+'('+trdata.left+') '+withs+' given has elapsed, the status of this issue will be automatically converted to UNRESOLVE.');
            }
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }
        $('#date').val(trdata.created_at);
        $('#status').val(trdata.status);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('table.requestDetails').dataTable().fnDestroy();
        $('table.schedDetails').dataTable().fnDestroy();

        if (trdata.status == 'PENDING') {
            $('#printBtn').hide();
            $('table.schedDetails').hide();
            //$('#unresolveBtn').hide();
            $('table.requestDetails').show();
            var pendreq;
            Promise.all([pendingrequest()]).then(() => { 
                if (pendreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (pendreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function pendingrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        pendreq = data.data.length;
                    },
                });
            }
        }else if (trdata.status == 'PARTIAL') {
            $('#printBtn').hide();
            $('table.schedDetails').hide();
            //$('#unresolveBtn').hide();
            $('table.requestDetails').show();
            var partreq;
            Promise.all([partialrequest()]).then(() => { 
                if (partreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
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
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (partreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
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
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function partialrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        partreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'SCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            //$('#unresolveBtn').hide();
            $('table.schedDetails').show();
            var schedreq;
            Promise.all([schedrequest()]).then(() => {
                if (schedreq <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (schedreq > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function schedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        schedreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'RESCHEDULED'){
            $('#printBtn').show();
            $('table.requestDetails').hide();
            //$('#unresolveBtn').hide();
            $('table.schedDetails').show();
            var resched;
            Promise.all([reschedrequest()]).then(() => {
                if (resched <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (resched > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function reschedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        resched = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'INCOMPLETE'){
            $('#printBtn').show();
            $('#printBtn').val("RESCHEDULE");
            //$('#unresolveBtn').show();
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            var incomp;
            Promise.all([incompleterequest()]).then(() => {
                if (incomp <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (incomp > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function incompleterequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        incomp = data.data.length;
                    },
                });
            }
        }
        
        $('#requestModal').modal('show');
    });

    searchtable =
    $('table.searchtable').DataTable({ 
        "dom": 't',
        "language": {
            "emptyTable": " "
        },
        "pageLength": 25,
        "order": [[ 1, "asc" ]],
        processing: true,
        serverSide: true,
        ajax: {
            "url": 'searchserial',
            error: function (data) {
                alert(data.responseText);
            }
        },
        columns: [
            { data: 'created_at', name:'date'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'},
            { data: 'branch', name:'branch'},
            { data: 'user', name:'user'}
        ]
    });
});
$(document).on("keyup", "#searchall", function () {
    if ($('#searchall').val()) {
        searchtable.columns(2).search(this.value).draw();
        //searchtable.search(this.value).draw();
        $('#searchtable').show();
        $('#salltable').show();
        $('#requestdiv').hide();
        $('#requestTable').hide();
    }else{
        $('#searchtable').hide();
        $('#salltable').hide();
        $('#requestdiv').show();
        $('#requestTable').show();
    }
});

var th = ['','thousand','million', 'billion','trillion'];
// uncomment this line for English Number System
// var th = ['','thousand','million', 'milliard','billion'];

var dg = ['zero','one','two','three','four', 'five','six','seven','eight','nine'];
var tn = ['ten','eleven','twelve','thirteen', 'fourteen','fifteen','sixteen', 'seventeen','eighteen','nineteen']; 
var tw = ['twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety']; 
function toWords(s){
    s = s.toString(); 
    s = s.replace(/[\, ]/g,''); 
    if (s != parseFloat(s)) return 'not a number'; 
    var x = s.indexOf('.'); 
    if (x == -1) x = s.length; 
    if (x > 15) return 'too big'; 
    var n = s.split(''); 
    var str = ''; 
    var sk = 0; 
    for (var i=0; i < x; i++) {
        if ((x-i)%3==2) {
            if (n[i] == '1') {
                str += tn[Number(n[i+1])] + ' '; 
                i++; 
                sk=1;
            } else if (n[i]!=0) {
                str += tw[n[i]-2] + ' ';
                sk=1;
            }
        } else if (n[i]!=0) {
            str += dg[n[i]] +' '; 
            if ((x-i)%3==0) str += 'hundred ';
            sk=1;
        } if ((x-i)%3==1) {
            if (sk) str += th[(x-i-1)/3] + ' ';
            sk=0;
        }
    } 
    if (x != s.length) {
        var y = s.length; 
        str += 'point '; 
        for (var i=x+1; i<y; i++) str += dg[n[i]] +' ';
    } 
    return str.replace(/\s+/g,' ');
}
