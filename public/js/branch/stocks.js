var y=1,r=1,b=1,table,branchid,stock;
$(document).ready(function(){branchid=$("#branchid").attr("branchid");table=$("table.stockTable").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,serverSide:!0,ajax:"viewStock",columns:[{data:"category",name:"category"},{data:"description",name:"description"},{data:"quantity",name:"quantity"}]});$(".tbsearch").delay().fadeOut("slow");$("#search-ic").on("click",function(){for(var a=0;6>=a;a++)$(".fl-"+a).val("").change(),table.columns(a).search("").draw();$(".tbsearch").toggle()});$(".filter-input").keyup(function(){table.column($(this).data("column")).search($(this).val()).draw()})});
$(document).on("click","#addStockBtn",function(){$("#addModal").modal({backdrop:"static",keyboard:!1})});$(document).on("click","#importBtn",function(){$("#importModal").modal({backdrop:"static",keyboard:!1})});$(document).on("click","#in_Btn",function(){"[]"==$("#check").val()?($(".defective").prop("disabled",!0),$(".good").prop("disabled",!0)):($(".defective").prop("disabled",!1),$(".good").prop("disabled",!1));$("#inOptionModal").modal({backdrop:"static",keyboard:!1})});
$(document).on("click","#out_Btn",function(){$("#outOptionModal").modal({backdrop:"static",keyboard:!1})});
$(document).on("click",".add_item",function(){var a=$(this).attr("btn_id");if("Add Item"==$(this).val()){if($("#category"+a).val()&&$("#item"+a).val()&&$("#desc"+a).val()&&$("#serial"+a).val()){y++;var d='<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+
y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';$(this).val("Remove");$("#category"+a).prop("disabled",!0);$("#item"+a).prop("disabled",!0);$("#desc"+a).prop("disabled",
!0);$("#serial"+a).prop("disabled",!0);20>r&&($("#reqfield").append(d),$("#category"+a).find("option").clone().appendTo("#category"+y),r++)}}else 20==r&&(y++,d='<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+
y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>',$("#reqfield").append(d),$("#category"+a).find("option").clone().appendTo("#category"+y),r++),$("#category"+a).val("select category"),
$("#item"+a).val("select item code"),$("#desc"+a).val("select description"),$("#serial"+a).val("select serial"),$("#category"+a).prop("disabled",!1),$("#item"+a).prop("disabled",!1),$("#desc"+a).prop("disabled",!1),$("#serial"+a).prop("disabled",!1),$("#row"+a).hide(),$(this).val("Add Item"),r--});$(document).on("click","#addCatBtn",function(){$("#addModal .close").click();$("#categoryModal").modal({backdrop:"static",keyboard:!1})});
$(document).on("click","#addCodeBtn",function(){$("#addModal .close").click();$("#itemModal").modal({backdrop:"static",keyboard:!1})});
$(document).on("click",".add_cat",function(){var a=$(this).attr("btn_id");console.log("1");if("Add"==$(this).val()){console.log("2");if($("#cat"+a).val()){y++;var d='<div class="row no-margin" id="catrow'+y+'"><div class="col-md-8 form-group"><input type="text" id="cat'+y+'" class="form-control serial" row_count="'+y+'" placeholder="Category"></div><div class="col-md-1 form-group"><input type="button" class="add_cat btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>';$(this).val("Remove");
$("#cat"+a).prop("disabled",!0);console.log("3");console.log("test"+y)}10>c&&($("#catfield").append(d),$("#cat"+a).find("option").clone().appendTo("#cat"+y),c++,console.log("4"))}else console.log("5"),10==c&&(y++,d='<div class="row no-margin" id="catrow'+y+'"><div class="col-md-8 form-group"><input type="text" id="cat'+y+'" class="form-control serial" row_count="'+y+'" placeholder="Category"></div><div class="col-md-1 form-group"><input type="button" class="add_cat btn btn-xs btn-primary" btn_id="'+
y+'" value="Add"></div></div>',$("#catfield").append(d),$("#cat"+a).find("option").clone().appendTo("#cat"+y),console.log("6"),c++),$("#cat"+a).val(""),$("#catrow"+a).hide(),$(this).val("Add"),c--});
$(document).on("click",".add_item-desc",function(){var a=$(this).attr("btn_id");console.log("1");if("Add"==$(this).val()){console.log("2");if($("#item-desc"+a).val()&&$("#itemcat"+a).val()){y++;var d='<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+
y+'" value="Add"></div></div>';$(this).val("Remove");$("#item-desc"+a).prop("disabled",!0);$("#itemcat"+a).prop("disabled",!0);console.log("3")}10>b&&($("#itemfield").append(d),$("#itemcat"+a).find("option").clone().appendTo("#itemcat"+y),b++,console.log("4"))}else console.log("5"),10==b&&(y++,d='<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+
y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>',$("#itemfield").append(d),$("#itemcat"+a).find("option").clone().appendTo("#itemcat"+y),console.log("6"),b++),$("#itemcat"+a).val(""),$("#itemrow"+a).hide(),$(this).val("Add"),b--});
$(document).on("click","#sub_cat_Btn",function(){for(var a,d=1,e=1;e<=y;e++)$("#catrow"+e).is(":visible")&&"Remove"==$(".add_cat[btn_id='"+e+"']").val()&&(d++,$("#sub_cat_Btn").prop("disabled",!0),a=$("#cat"+e).val(),$.ajax({url:"addcategory",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"POST",data:{cat:a}}));1<d&&(window.location.href="stocks")});
$(document).on("click","#sub_item_Btn",function(){var a=1;console.log(y);for(var d=1;d<=y;d++)if($("#itemrow"+d).is(":visible")&&(console.log(y),"Remove"==$(".add_item-desc[btn_id='"+d+"']").val())){a++;$("#sub_item_Btn").prop("disabled",!0);var e=$("#itemcat"+d).val();item=$("#item-desc"+d).val();$.ajax({url:"additem",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},dataType:"json",type:"POST",data:{cat:e,item:item}})}1<a&&(window.location.href="stocks")});
$(document).on("click",".cancel",function(){window.location.href="stocks"});
$(document).on("keyup","#client",function(){var a=$(this).val(),d=" ";$("#customer").val("");$("#customer-name").find("option").remove();(function(e){$.ajax({type:"get",url:"client-autocomplete",data:{id:a},success:function(g){d+=" ";for(var f=0;f<g.length;f++)d+='<option data-value="'+g[f].id+'" value="'+g[f].customer.toUpperCase()+'"></option>';$("#client-name").find("option").remove().end().append(d);$("#client-id").val($('#client-name [value="'+$("#client").val()+'"]').data("value"))}})})(client)});
$(document).on("keyup","#customer",function(){var a=$(this).val(),d=" ";if($("#client-id").val())var e=$("#client-id").val();else return alert("Incomplete Client Name!!!!"),!1;(function(g){$.ajax({type:"get",url:"customer-autocomplete",data:{id:a,client:e},success:function(f){d+=" ";for(var h=0;h<f.length;h++)d+='<option data-value="'+f[h].id+'" value="'+f[h].customer_branch.toUpperCase()+'"></option>';$("#customer-name").find("option").remove().end().append(d);$("#customer-id").val($('#customer-name [value="'+
$("#customer").val()+'"]').data("value"))}})})(customer)});
$(document).on("click","#stockTable tr",function(){$("#stockTable tbody tr:eq(0)").data();var a=table.row(this).data(),d=a.items_id;$("table.stockDetails").dataTable().fnDestroy();$("#head").text(a.category);$("#stockModal").modal();stock=$("table.stockDetails").DataTable({dom:"rt",language:{emptyTable:"No Stock Available for this Item"},processing:!0,serverSide:!0,ajax:"/bserial/"+d,columns:[{data:"updated_at",name:"updated_at"},{data:"item",name:"item"},{data:"serial",name:"serial"}]})});