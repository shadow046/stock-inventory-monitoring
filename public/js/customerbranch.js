var table;
$(document).ready(function(){var a=window.location.pathname;a=a.substring(a.lastIndexOf("/")+1);console.log(a);table=$("table.customerbranchTable").DataTable({dom:"lrtip",language:{emptyTable:" "},processing:!0,serverSide:!1,ajax:"/customerbranch-list/"+a,columns:[{data:"code",name:"code"},{data:"customer_branch",name:"customer_branch"},{data:"contact",name:"contact"},{data:"status",name:"status"}]});$(".tbsearch").delay().fadeOut("slow");$("#search-ic").on("click",function(b){for(b=0;6>=b;b++)$(".fl-"+
b).val("").change(),table.columns(b).search("").draw();$(".tbsearch").toggle()});$(".filter-input").keyup(function(){table.column($(this).data("column")).search($(this).val()).draw()})});