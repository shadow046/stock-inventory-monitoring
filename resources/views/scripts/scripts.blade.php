
<script type="text/javascript">
    $(document).ready(function()
    {
        $(document).on('change','.area',function()
        {
            var id=$(this).val();
            var sel=$(this).parent();
            var op=" ";
            //console.log(id);
            $.ajax({
                type:'get',
                url:'{!!URL::to('getBranchName')!!}',
                data:{'id':id},
                success:function(data)
                {
                    //console.log('success');
                    //console.log(data);
                    //console.log(data.length);
                    op+='<option selected disabled>select branch</option>';
                    for(var i=0;i<data.length;i++){
                        op+='<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                    $('#branch').find('option').remove().end().append(op);
                },
                
            });
        });

        $('#addBtn').on('click', function(e){
            e.preventDefault();
            $('#subBtn').val('Save');
            addBtn = $('#addBtn').val();
            if(addBtn == 'Add Branch'){
                $('#branchModal').modal('show');
                $('#branch_name').val('');
                $('#address').val('');
                $('#area').val('select area');
                $('#contact_person').val('');
                $('#mobile').val('');
                $('#email').val('');
                $('#status').val('select status');
            }
            if(addBtn == 'New User'){
                $("#divpass1").show();
                $("#divpass2").show();
                $('#userModal').modal('show');
                $('#full_name').val('');
                $('#email').val('');
                $('#password').val('');
                $('#password_confirmation').val('');
                $('role').val('select role')
                $('#area').val('select area');
                $('#branch').val('select branch');
                $('#status').val('select status');
            }
        });

        $('.edittr').on('click', function(){
            $('#subBtn').val('Update');
            addBtn = $('#addBtn').val();
            if(addBtn == 'Add Branch'){
                $tr = $(this).closest('tr');
                var id = $(this).attr('data-id');
                var area = $(this).attr('data-area');
                var status = $(this).attr('data-status');
                var data = $tr.children("td").map(function(){
                    return $(this).text();
                }).get();

                $('#branch_name').val($.trim(data[0]));
                $('#address').val($.trim(data[1]));
                $('#area').val(area);
                $('#contact_person').val($.trim(data[3]));
                $('#mobile').val($.trim(data[4]));
                $('#email').val($.trim(data[5]));
                $('#status').val(status);
                $('#myid').val(id);  
            }
            if(addBtn == 'New User'){
                $tr = $(this).closest('tr');
                var id = $(this).attr('data-id');
                var area = $(this).attr('data-area');
                var branch = $(this).attr('data-branch');
                var role = $(this).attr('data-role');
                var status = $(this).attr('data-status');
                var op=" ";
                var info = $tr.children("td").map(function(){
                    return $(this).text();
                }).get();
                $("#divpass1").hide();
                $("#divpass2").hide();
                selectBranch(branch, function() {
                    $('#full_name').val($.trim(info[0]));
                    $('#email').val($.trim(info[1]));
                    $('#area').val(area);
                    $('#branch').val(branch);
                    $('#role').val(role);
                    $('#status').val(status);
                    $('#myid').val(id);
                });
            }

            function selectBranch(branch, callback) {
                $.ajax({
                    type:'get',
                    url:'{!!URL::to('getBranchName')!!}',
                    data:{'id':area},
                    success:function(data)
                    {
                        //console.log('success');
                        //console.log(data);
                        //console.log(data.length);
                        op+='<option selected disabled>select branch</option>';
                        for(var i=0;i<data.length;i++){
                            op+='<option value="'+data[i].id+'">'+data[i].name+'</option>';
                        }
                        $('#branch').find('option').remove().end().append(op);
                        callback();
                    },
                });
            }
            
        });

        $('#branchForm').on('submit', function(e){
            e.preventDefault();
            subBtn = $('#subBtn').val();
            if(subBtn == 'Update'){
                var myid = $('#myid').val();
                $.ajax({
                    type: "PUT",
                    url: "/service_center_update/"+myid,
                    data: $('#branchForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#branchModal').modal('hide');
                            alert("Data Updated");
                            window.location.reload();
                        }else{
                            alert(data.error);
                        }
                    } 
                });
            }
            if(subBtn == 'Save'){
                $.ajax({
                    type: "POST",
                    url: "/service_center_add/",
                    data: $('#branchForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#branchModal').modal('hide');
                            alert("Data Saved");
                            window.location.reload();
                        }else{
                            alert(data.error);
                        }
                    }
                });
            }
        });

        $('#userForm').on('submit', function(e){
            e.preventDefault();
            subBtn = $('#subBtn').val();
            if(subBtn == 'Update'){
                var myid = $('#myid').val();
                $.ajax({
                    type: "PUT",
                    url: "/user_up/"+myid,
                    data: $('#userForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#userModal').modal('hide');
                            alert("Data Updated");
                            window.location.reload();
                        }else{
                            alert(data.error);
                        }
                    } 
                });
            }
            if(subBtn == 'Save'){
                $.ajax({
                    type: "POST",
                    url: "/user_add/",
                    data: $('#userForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#userModal').modal('hide');
                            alert("Data Saved");
                            window.location.reload();
                        }else{
                            alert(data.error);
                        }
                    }
                });
            }
        });
    });
</script>
