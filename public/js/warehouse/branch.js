var stockTable,table;$(document).on("click",function(a){$('[data-toggle="popover"]').each(function(){!$(this).is(a.target)&&0===$(this).has(a.target).length&&0===$(".popover").has(a.target).length&&$(this).data("bs.popover")&&((($(this).popover("hide").data("bs.popover")||{}).inState||{}).click=!1)})});
$(document).ready(function(){$("#saveBtn").hide();table=$("table.branchTable").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,serverSide:!0,ajax:{url:"branches",error:function(a,b,c){401==a.status&&(window.location.href="/login")}},columns:[{data:"branch",name:"branch",width:"14%"},{data:"area",name:"area",width:"14%"},{data:"head",name:"head",width:"14%"},{data:"phone",name:"phone",width:"14%"},{data:"email",name:"email",width:"14%"},{data:"status",name:"status",width:"14%"},{data:"address",
name:"address",width:"14%"}]});$("#branchTable tbody").on("click","tr",function(){var a=$("#branchTable tbody tr:eq(0)").data(),b=table.row(this).data(),c=b.id;console.log(b.id);$("table.branchDetails").dataTable().fnDestroy();$("#table").show();stockTable=$("table.branchDetails").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,serverSide:!0,ajax:"/stocks/"+c,columns:[{data:"item",name:"item",width:"17%"},{data:"initial",name:"initial",width:"17%"},{data:"available",name:"available",
width:"14%"},{data:"stock_out",name:"stock_out",width:"14%"}]});$("#branch_name").prop("disabled",!0);$("#address").prop("disabled",!0);$("#area").prop("disabled",!0);$("#contact_person").prop("disabled",!0);$("#mobile").prop("disabled",!0);$("#email").prop("disabled",!0);$("#status").prop("disabled",!0);$("#myid").val(b.id);$("#branch_name").val(b.branch);$("#address").val(b.address);$("#area").val(b.area_id);$("#contact_person").val(b.head);$("#mobile").val(b.phone);$("#email").val(b.email);$("#status").val(a.dataStatus);
$("#myid").val(b.id);$("#editBtn").val("Edit");$("#editBtn").show();$("#saveBtn").hide();$("#branchModal").modal("show")});$("#addBtn").on("click",function(a){a.preventDefault();$("#branchModal").modal("show");$("#branch_name").val("");$("#address").val("");$("#area").val("select area");$("#contact_person").val("");$("#mobile").val("");$("#email").val("");$("#status").val("select status");$("#branch_name").prop("disabled",!1);$("#address").prop("disabled",!1);$("#area").prop("disabled",!1);$("#contact_person").prop("disabled",
!1);$("#mobile").prop("disabled",!1);$("#email").prop("disabled",!1);$("#status").prop("disabled",!1);$("#editBtn").val("Save");$("#editBtn").hide();$("#saveBtn").show();$("#table").hide()});$("#editBtn").on("click",function(){$("#branch_name").prop("disabled",!1);$("#address").prop("disabled",!1);$("#area").prop("disabled",!1);$("#contact_person").prop("disabled",!1);$("#mobile").prop("disabled",!1);$("#email").prop("disabled",!1);$("#status").prop("disabled",!1);$("#editBtn").hide();$("#saveBtn").show()});
$("#branchForm").on("submit",function(a){a.preventDefault();editBtn=$("#editBtn").val();"Edit"==editBtn&&(a=$("#myid").val(),$.ajax({type:"PUT",url:"/branch_update/"+a,headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:$("#branchForm").serialize(),success:function(b){$.isEmptyObject(b.error)?($("#branchModal .close").click(),table.draw()):alert(b.error)}}));"Save"==editBtn&&$.ajax({type:"POST",url:"branch_add",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},
data:$("#branchForm").serialize(),success:function(b){$.isEmptyObject(b.error)?($("#branchModal .close").click(),table.draw()):alert(b.error)}})});$("#filter").popover({html:!0,sanitize:!1,title:"Filter Columns &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"});$("#filter").on("click",function(a){for(a=1;6>=a;a++)table.column(a).visible()?$("#filter-"+a).prop("checked",!0):$("#filter-"+a).prop("checked",!1)});$("body").on("click",
".branchColumnCb",function(){var a=table.column($(this).attr("data-column")),b=$(this).attr("data-column");$(".fl-"+b).val("");table.columns(b).search("").draw();a.visible(!a.visible())});$("#search-ic").on("click",function(a){for(a=0;6>=a;a++)$(".fl-"+a).val("").change(),table.columns(a).search("").draw();$(".tbsearch").toggle()});$(".filter-input").keyup(function(){table.column($(this).data("column")).search($(this).val()).draw()});$(".mfilter-input").keyup(function(){stockTable.column($(this).data("column")).search($(this).val()).draw()})});
$(document).on("click","#branchDetails tr",function(){var a=stockTable.row(this).data(),b=a.item;$("#head4").text(b.replace(/&quot;/g,'"'));$("#item-qty").val(a.initial);$("#iniitemid").val(a.items_id);$("#inibranchid").val(a.branch_id);$("#updateModal").modal({backdrop:"static",keyboard:!1})});
$(document).on("click","#updateBtn",function(){var a=$("#iniitemid").val(),b=$("#inibranchid").val(),c=$("#item-qty").val();$.ajax({url:"branch_ini",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"PUT",data:{itemid:a,branchid:b,qty:c},success:function(d){$("table.branchDetails").dataTable().fnDestroy();stockTable=$("table.branchDetails").DataTable({dom:"lrtip",language:{emptyTable:" ",processing:"Updating. Please wait.."},processing:!0,serverSide:!0,ajax:"/stocks/"+
b,columns:[{data:"item",name:"item",width:"17%"},{data:"initial",name:"initial",width:"17%"},{data:"available",name:"available",width:"14%"},{data:"stock_out",name:"stock_out",width:"14%"}]});$("#updateModal .close").click()}})});