var table,interval=null;
$(document).ready(function(){table=$("table.defectiveTable").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,serverSide:!0,ajax:{url:"return-table",error:function(a,d,c){401==a.status&&(window.location.href="/login")}},columns:[{data:"date",name:"date"},{data:"branch",name:"branch"},{data:"category",name:"category"},{data:"item",name:"item"},{data:"serial",name:"serial"},{data:"status",name:"status"}]});interval=setInterval(function(){table.draw()},3E4);$("#search-ic").on("click",
function(){for(var a=0;5>=a;a++)$(".fl-"+a).val("").change(),table.columns(a).search("").draw();$(".tbsearch").toggle()});$(".filter-input").keyup(function(){table.column($(this).data("column")).search($(this).val()).draw()})});
$(document).on("click","#defectiveTable tr",function(){var a=table.row(this).data();console.log(a);clearInterval(interval);$("#branch_id").val(a.branchid);$("#date").val(a.date);$("#description").val(a.item);$("#status").val(a.status);$("#myid").val(a.id);$("#serial").val(a.serial);"For receiving"==a.status?($("#submit_Btn").val("Received"),$("#submit_Btn").show()):"For repair"==a.status&&"Repair"==$("#level").val()?($("#submit_Btn").val("Repaired"),$("#submit_Btn").show()):"Repaired"==a.status?($("#submit_Btn").val("Add to stock"),
$("#submit_Btn").show()):$("#submit_Btn").hide();$("#returnModal").modal({backdrop:"static",keyboard:!1})});
$(document).on("click","#submit_Btn",function(){var a=$("#branch_id").val(),d=$("#myid").val(),c=$("#submit_Btn").val();"Received"==$("#submit_Btn").val()&&$.ajax({url:"return-update",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"PUT",data:{id:d,branch:a,status:c},success:function(b){interval=setInterval(function(){table.draw()},3E4);table.draw();$("#returnModal .close").click()},error:function(b,e,f){alert(b.responseText)}});"Repaired"==$("#submit_Btn").val()&&
$.ajax({url:"return-update",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"PUT",data:{id:d,branch:a,status:c},success:function(b){interval=setInterval(function(){table.draw()},3E4);table.draw();$("#returnModal .close").click()},error:function(b,e,f){alert(b.responseText)}});"Add to stock"==$("#submit_Btn").val()&&(c="warehouse",$.ajax({url:"return-update",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"PUT",data:{id:d,
branch:a,status:c},success:function(b){interval=setInterval(function(){table.draw()},3E4);table.draw();$("#returnModal .close").click()},error:function(b,e,f){alert(b.responseText)}}))});$(document).on("click",".close",function(){interval=setInterval(function(){table.draw()},3E4);table.draw()});
$(document).on("click","#unrepair_Btn",function(){var a=$("#branch_id").val(),d=$("#myid").val();$.ajax({url:"return-update",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"PUT",data:{id:d,branch:a,status:"unrepairable"},success:function(c){interval=setInterval(function(){table.draw()},3E4);table.draw();$("#returnModal .close").click()},error:function(c,b,e){alert(c.responseText)}})});