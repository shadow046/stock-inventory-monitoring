var table,interval=null;
$(document).ready(function(){table=$("table.defectiveTable").DataTable({dom:"lrtip",processing:!0,serverSide:!0,language:{emptyTable:" "},ajax:{url:"return-table",error:function(a,b,d){401==a.status&&(window.location.href="/login")}},columns:[{data:"date",name:"date"},{data:"category",name:"category"},{data:"item",name:"item"},{data:"serial",name:"serial"},{data:"status",name:"status"}]});interval=setInterval(function(){table.draw()},3E4);$("#search-ic").on("click",
function(){for(var a=0;5>=a;a++)$(".fl-"+a).val("").change(),table.columns(a).search("").draw();$(".tbsearch").toggle()});$(".filter-input").keyup(function(){table.column($(this).data("column")).search($(this).val()).draw()})});
$(document).on("click","#defectiveTable tr",function(){var a=table.row(this).data();clearInterval(interval);$("#branch_id").val(a.branchid);$("#date").val(a.date);$("#description").val(function(b){return b.replace(/&#(?:x([\da-f]+)|(\d+));/ig,function(d,c,e){return String.fromCharCode(e||+("0x"+c))})}(a.item));$("#status").val(a.status);$("#myid").val(a.id);$("#serial").val(a.serial);$("#return_id").val(a.itemid);"For return"==a.status?$("#submit_Btn").show():$("#submit_Btn").hide();$("#returnModal").modal({backdrop:"static",
keyboard:!1})});$(document).on("click","#submit_Btn",function(){var a=$("#branch_id").val(),b=$("#myid").val(),d=$("#return_id").val();$.ajax({url:"return-update",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"PUT",data:{id:b,branch:a,status:"For receiving",itemid:d},success:function(c){interval=setInterval(function(){table.draw()},3E4);table.draw();$("#returnModal .close").click()},error:function(c,e,f){alert(c.responseText)}})});
$(document).on("click",".cancel",function(){window.location.href="return"});$(document).on("click",".close",function(){table.draw();interval=setInterval(function(){table.draw()},3E4)});