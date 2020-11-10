var y=1,interval=null,table;
$(document).ready(function(){var a=new Date,c=String(a.getHours()).padStart(2,"0")%12||12,d=12>String(a.getHours()).padStart(2,"0")||24===String(a.getHours()).padStart(2,"0")?"AM":"PM",e="January February March April May June July August September October November December".split(" ");$("#date").val(e[a.getMonth()]+" "+a.getDate()+", "+a.getFullYear()+" "+c+":"+String(a.getMinutes()).padStart(2,"0")+d);$("#sdate").val(e[a.getMonth()]+" "+a.getDate()+", "+a.getFullYear()+" "+c+":"+String(a.getMinutes()).padStart(2,
"0")+d);table=$("table.requestTable").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,serverSide:!0,ajax:"requests",columns:[{data:"created_at",name:"date",width:"14%"},{data:"request_no",name:"request_no",width:"14%"},{data:"reqBy",name:"reqBy",width:"14%"},{data:"status",name:"status",width:"14%"}]});interval=setInterval(function(){table.draw()},3E4);$("#requestTable tbody").on("click","tr",function(){clearInterval(interval);var b=table.row(this).data();$("#requestTable tbody tr:eq(0)").data();
$("#date").val(b.created_at);$("#reqno").val(b.request_no);$("#branch").val(b.branch);$("#name").val(b.reqBy);$("#area").val(b.area);$("table.requestDetails").dataTable().fnDestroy();$("table.schedDetails").dataTable().fnDestroy();"PENDING"==b.status?($("table.schedDetails").hide(),$("table.requestDetails").show(),$(".sched").hide(),$("#del_Btn").show(),$("#rec_Btn").hide(),$("#del_Btn").attr("reqno",b.request_no),$("table.requestDetails").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,
serverSide:!0,ajax:"/requests/"+b.request_no,columns:[{data:"items_id",name:"items_id"},{data:"item_name",name:"item_name"},{data:"quantity",name:"quantity"},{data:"purpose",name:"purpose"}]})):"SCHEDULED"==b.status&&($("table.requestDetails").hide(),$(".sched").show(),$("table.schedDetails").show(),$("#sched").val(b.sched),$("#del_Btn").hide(),$("#rec_Btn").show(),$("table.schedDetails").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,serverSide:!0,ajax:"/send/"+b.request_no,columns:[{data:"items_id",
name:"items_id"},{data:"item_name",name:"item_name"},{data:"serial",name:"serial"}]}));$("#requestModal").modal("show")})});$(document).on("click","#del_Btn",function(){var a=$(this).attr("reqno");$.ajax({url:"remove",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"DELETE",data:{reqno:a},success:function(){table.draw();interval=setInterval(function(){table.draw()},3E4);$("#requestModal .close").click()}})});
$(document).on("click","#rec_Btn",function(){var a=$("#reqno").val(),c=$("#sched").val();console.log(c);$.ajax({url:"update",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"PUT",data:{reqno:a,status:"2",datesched:c,stat:"ok"},success:function(){},error:function(d,e,b){alert(d.responseText)}});$.ajax({url:"storerreceived",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"POST",data:{reqno:a},success:function(){table.draw();
interval=setInterval(function(){table.draw()},3E4);$("#requestModal .close").click()},error:function(d,e,b){alert(d.responseText)}})});$(document).on("click","#reqBtn",function(){clearInterval(interval);$.ajax({type:"get",url:"gen",async:!1,success:function(a){$("#sreqno").val(a)}});$("#sendrequestModal").modal({backdrop:"static",keyboard:!1})});
$(document).on("click",".add_item",function(){var a=$(this).attr("btn_id");if("Add Item"==$(this).val())if(0!=$("#qty"+a).val()&&$("#purpose"+a).val()){y++;var c='<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black;" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" style="color: black;" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+
y+'" class="form-control desc" style="color: black;" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><select id="purpose'+y+'" class="form-control purpose" style="color: black;" row_count="'+y+'"><option selected disabled>select purpose</option><option value="1">Service Unit</option><option value="2">Replacement</option><option value="3">Stock</option></select></div><div class="col-md-2 form-group"><input type="number" min="0" class="form-control" style="color: black; width: 6em" name="qty'+
y+'" id="qty'+y+'" placeholder="0" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';$(this).val("Remove");$("#category"+a).prop("disabled",!0);$("#item"+a).prop("disabled",!0);$("#desc"+a).prop("disabled",!0);$("#qty"+a).prop("disabled",!0);$("#purpose"+a).prop("disabled",!0);$("#reqfield").append(c);$("#category"+a).find("option").clone().appendTo("#category"+y)}else alert("Invalid Quantity value!!!");
else $("#category"+a).val("select category"),$("#item"+a).val("select item code"),$("#desc"+a).val("select description"),$("#serial"+a).val("select serial"),$("#purpose"+a).val("select purpose"),$("#category"+a).prop("disabled",!1),$("#item"+a).prop("disabled",!1),$("#desc"+a).prop("disabled",!1),$("#serial"+a).prop("disabled",!1),$("#purpose"+a).prop("disabled",!1),$("#row"+a).hide(),$(this).val("Add Item")});
$(document).on("click",".send_sub_Btn",function(a){a.preventDefault();for(var c,d="notok",e=$("#sreqno").val(),b=1;b<=y;b++)$("#row"+b).is(":visible")&&"Remove"==$(".add_item[btn_id='"+b+"']").val()&&($("#category"+b).val(),a=$("#item"+b).val(),$("#desc"+b).val(),c=$("#qty"+b).val(),purpose=$("#purpose"+b).val(),$.ajax({url:"storerequest",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"POST",data:{reqno:e,item:a,purpose:purpose,qty:c,stat:d}})),b==y&&(d=
"ok",$.ajax({url:"storerequest",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"POST",data:{reqno:e,stat:d}}),window.location.href="request")});$(document).on("change",".desc",function(){var a=$(this).attr("row_count"),c=$(this).val();$("#item"+a).val(c);$("#qty"+a).prop("disabled",!1)});$(document).on("change",".item",function(){var a=$(this).attr("row_count"),c=$(this).val();$("#desc"+a).val(c);$("#qty"+a).prop("disabled",!1)});
$(document).on("change",".category",function(){var a=" ",c=" ",d=$(this).attr("row_count"),e=$(this).val();$("#stock"+d).val("Stock");(function(b){$.ajax({type:"get",url:"itemcode",data:{id:e},success:function(g){a+='<option selected value="select" disabled>select item code</option>';c+='<option selected value="select" disabled>select description</option>';for(var f=0;f<g.length;f++)a+='<option value="'+g[f].id+'">'+g[f].id+"</option>",c+='<option value="'+g[f].id+'">'+g[f].item.toUpperCase()+"</option>";
$("#item"+d).find("option").remove().end().append(a);$("#desc"+d).find("option").remove().end().append(c)}})})(item1);$("#item"+d).val("select itemcode");$("#desc"+d).val("select description");$("#item"+d).css("border","")});$(document).on("click",".close",function(){table.draw();interval=setInterval(function(){table.draw()},3E4)});