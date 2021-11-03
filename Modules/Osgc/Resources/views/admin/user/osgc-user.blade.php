@extends('adminlte::page')
@section('title', 'Registered Users')
@section('content_header')
<h1>Registered Users</h1>
@stop
@section('content')
<a class="user-export btn"  href="{{route('osgc-users.user-export')}}">User Export</a>
<table class="table table-bordered" id="user-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Registered On</th>
            <th>Veteran Status</th>
            <th>Aboriginal descent Status</th>
            <th>Referral</th>
            <th>Payment Status</th>
            <th>Amount</th>
            <th>Course Name</th>
            <th>Last Module completed</th>
            <th>% Completed</th>
            <th>Days Tracker</th>
            <th>Registered Month</th>
            <th>Status</th>
            <th>Action</th>
            
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Registered Users</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'user-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
           
            
            <div class="modal-body">
                <div class="form-group" id="first_name">
                    <label for="first_name" class="col-sm-3 control-label">First Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('first_name',null,array('class'=>'form-control', 'Placeholder'=>'First Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="last_name">
                    <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('last_name',null,array('class'=>'form-control', 'Placeholder'=>'Last Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="is_veteran">
                    <label for="is_veteran" class="col-sm-3 control-label">Are you veteran?</label>
                    <div class="col-sm-9">
                       <label> <input type="radio" name="is_veteran"  value="1" >&nbsp;Yes&nbsp;&nbsp;</label>
                        <label> <input type="radio" name="is_veteran"  checked value="0" >&nbsp;No&nbsp;&nbsp;</label> 
                    
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="indian_status">
                    <label for="indian_status" class="col-sm-3 control-label">Are you of aboriginal descent?</label>
                    <div class="col-sm-9">
                    <select  name="indian_status"   class="form-control"  id="indian_status">
                            <option value="">Select</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                            
                    </select>
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop 
@section('js')
<style>
 .dataTable a.activate,a.inactivate {
    padding-right: 8px;
}
.user-export{
    float: right;
}
.user-export {
    float: right;
    width: 200px;
    background-color: #f26222;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 10px;
    text-align: center;
    border-radius: 5px;
    padding: 5px 0px;
    margin-left: 5px;
    cursor: pointer;
    border: 0px;
}
</style>
<script>
    $(function () {

            $.fn.dataTable.ext.errMode = 'throw';
        try{
            
        var table = $('#user-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('osgc-users.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // order: [
            //     [10, "desc"]
            // ],
            "rowCallback": function (row, data, index) {
                var bg_color =  data.background_color;
                var color =  data.color;
                $(row).find('td:eq(10)').css('background-color', bg_color).css('color',color);

            },  
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            "autoWidth": false,
            "columns": [
                { "width": "2%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "5%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
                { "width": "3%" },
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    "className": "text-center",
                    data: 'is_veteran',
                    name: 'is_veteran'
                },
                {
                    "className": "text-center",
                    data: 'indian_status',
                    name: 'indian_status'
                },
                {
                    "className": "text-center",
                    data: 'referral',
                    name: 'referral'
                },
                {
                    "className": "text-center",
                    data: 'status',
                    name: 'status'
                },
                
                {
                    "className": "text-center",
                    data: 'amount',
                    name: 'amount'
                },
                 
                {
                    data: 'course_title',
                    name: 'course_title'
                },
                 {
                    data: 'last_course_completion',
                    name: 'last_course_completion'
                },
                {
                    "className": "text-center",
                    data: 'percentage_completion',
                    name: 'percentage_completion'
                },
                {
                    "className": "text-center",
                    data: 'days_tracker',
                    name: 'days_tracker'
                },
               
                {
                    data: 'paid_date',
                    name: 'paid_date'
                },
               
                {
                    data: 'active',
                    name: 'active'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                       
                        
                        var actions = '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
                        
                        if(o.active =='Active')
                        {
                             actions += '<a href="#" class="inactivate fa fa-check" data-id=' + o.id + ' title="Activated"></a>'
                            
                        }else{
                             actions += '<a href="#" class="activate fa fa-times" data-id=' + o.id + ' title="Deactivated"></a>'
                            
                        }
                        actions += '<a href="#" class="password-reset fa fa-key" data-email=' + o.email + ' title="Password Reset"></a>'
                        return actions;
                    },
                }
                
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
       
$('#user-form').submit(function (e) {
    e.preventDefault();
    var message = 'User updated successfully';
    formSubmit($('#user-form'), "{{ route('osgc-user.store') }}", table, e, message);
});       
$('#user-table').on('click', '.activate', function (e) {

var id = $(this).data('id');
var base_url = "{{ route('osgc-users.activateUsers',':id') }}";
var url = base_url.replace(':id', id);
var message = 'Do you want to activate this user?';
var flag='Activated';
var successmsg='User has been updated successfully';
activateordeactivateRecord(url, table, message,flag,successmsg);

});
$('#user-table').on('click', '.inactivate', function (e) {

var id = $(this).data('id');
var base_url = "{{ route('osgc-users.deactivateUsers',':id') }}";
var url = base_url.replace(':id', id);
var message = 'Do you want to deactivate this user?';
var flag='Deactivated';
var successmsg='User has been updated successfully';
activateordeactivateRecord(url, table, message,flag,successmsg);

});
/* activate or deactivate Record - Start */
$('#user-table').on('click', '.password-reset', function (e) {

var emailId = $(this).data('email');
var base_url = "{{ route('osgc-users.reset-password',':email') }}";
var url = base_url.replace(':email', emailId);
var message = 'Do you want to reset the password ?';
var flag='Success';
var successmsg='Password has been reset and the same is sent to the registered mail id';
activateordeactivateRecord(url, table, message,flag,successmsg);

});
function activateordeactivateRecord(url, table, message,flag,successmsg) {
    var url = url;
    var table = table;
    swal({
        title: "Are you sure?",
        text: message,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        closeOnConfirm: false
    },
    function () {
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    swal(flag, successmsg, "success");
                    if (table != null) {
                        table.ajax.reload();
                    }
                } else {
                    if(response.message)
                    {
                        var msg=response.message;
                    }else{
                        var msg='Please try again';
                    }
                    swal("Warning", msg, "warning");
                    
                } 
            },
            error: function (xhr, textStatus, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            },
            contentType: false,
            processData: false,
        });
    });
}
 /* activate or deactivate Record - End */
 $("#user-table").on("click", ".edit", function (e) {
            
            var id = $(this).data('id');
            var url = '{{ route("osgc-user.single",":id") }}';
            var url = url.replace(':id', id);
            $('#user-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    console.log(data)
                    $("#user-form").trigger('reset');
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="first_name"]').val(data.first_name)
                        $('#myModal input[name="last_name"]').val(data.last_name)
                        $('#myModal input:radio[name="is_veteran"][value=' + data.is_veteran + ']').prop('checked', true)
                        $('#myModal select[name="indian_status"] option[value="'+data.indian_status+'"]').prop('selected',true);
                       
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit OSGC User: "+ data.first_name)
                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });      
       
       
    });
    
</script>
@stop
