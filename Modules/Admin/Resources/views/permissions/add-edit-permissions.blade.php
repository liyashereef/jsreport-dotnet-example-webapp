 @extends('adminlte::page')
 @section('title', 'Roles And Permissions')
 @section('content_header')
<h3>Roles and Permissions</h3>
@stop

@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('url'=>'#','id'=>'role-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    {{ Form::hidden('id',$data['id']) }}
    <section class="content">
        <div class="form-group {{ $errors->has('role') ? 'has-error' : '' }}" id="role">
            <label for="role" class="col-form-label col-md-2">Role
                <span class="mandatory">*</span>
            </label>
            <div class="col-md-10">
            <?php $readonly = (in_array($data['role_name'], $existing_roles)) ? true : false?>
                {{ Form::text('role', $data['role_name'],array('class'=>'form-control', 'placeholder'=>'Role','readonly'=>$readonly)) }}
                <span class="help-block"></span>
            </div>
        </div>
        <table class="table table-bordered" style="width:60%">
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'login',in_array('login', $data['permission_name']))}} Web Login</label></td>
            <td><label>This permission is used to login to CGL 360 Web</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'super_admin',in_array('super_admin', $data['permission_name']))}} Super Admin</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Super Admin</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'admin',in_array('admin', $data['permission_name']))}} Admin</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Admin</label></td>

        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'client',in_array('client', $data['permission_name']))}} Client</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Client</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'area_manager',in_array('area_manager', $data['permission_name']))}} Area Manager</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Regional Manager</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'cfo',in_array('cfo', $data['permission_name']))}} CFO</label></td>
            <td><label>This permission is used to login to setup the fundamental role as CFO</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'manager',in_array('manager', $data['permission_name']))}} Manager</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Manager</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'hr_manager',in_array('hr_manager', $data['permission_name']))}} Hr Manager</label></td>
            <td><label>This permission is used to login to setup the fundamental role as HR Manager</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'hr_representative',in_array('hr_representative', $data['permission_name']))}} Hr Representative</label></td>
            <td><label>This permission is used to login to setup the fundamental role as HR Representative</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'duty_officer',in_array('duty_officer', $data['permission_name']))}} Duty Officer</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Duty Officer</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'employee',in_array('employee', $data['permission_name']))}} HQ Staff</label></td>
            <td><label>This permission is used to login to setup the fundamental role as HQ Staff</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'supervisor',in_array('supervisor', $data['permission_name']))}} Supervisor</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Supervisor</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'guard',in_array('guard', $data['permission_name']))}} Guard</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Guard</label></td>
        </tr>
        <tr>
            <td><label>{{ Form::checkbox('module_permissions[]', 'Spares Pool',in_array('Spares Pool', $data['permission_name']))}} Spares Pool</label></td>
            <td><label>This permission is used to login to setup the fundamental role as Spares Pool</label></td>
        </tr>
        </table>
        <table class="table table-bordered" style="width:60%">

            @foreach($data['modules'] as $each_module)
                <tr data-class="{{$each_module->id}}_group">
                    <th><label>{{ Form::checkbox('module_permissions[]', 'view_'.strtolower(str_replace(" ","", $each_module->name)),in_array('view_'.strtolower(str_replace(" ","", $each_module->name)), $data['permission_name']),array('id'=>$each_module->id.'_id'))}} {{$each_module->name}}</label></th>
                </tr>
                @foreach($each_module->permission_model as $permissions)
                    <tr class="{{$each_module->id}}_group" data-header-id="{{$each_module->id}}_id">
                        <td><label>{{ Form::checkbox('module_permissions[]', $permissions->permission->name, in_array($permissions->permission->id,$data['permission_array']),array('class'=>$each_module->id.'_group')) }} {{$permissions->permission_description}}</label></td>
                    </tr>
                @endforeach
            @endforeach
        </table>
</div>
<div class="modal-footer">
    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
     <a href="{{ route('role') }}" class="btn btn-primary blue">Cancel</a>
    {{ Form::close() }}
</div>
@stop @section('js')
<script type="text/javascript">
    $(function () {
        //save form data
        $('#role-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#role-form')[0]);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('role.store') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: "Saved",
                            text: "The permissions has been set",
                            type: "success",
                            confirmButtonText: "OK",
                            showLoaderOnConfirm: true,
                            closeOnConfirm: false
                        });
                    } else {
                        alert(data.success);
                    }
                },
                fail: function (response) {
                    alert('here');

                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });

        //set permission on check and uncheck of module name
        $("input[name='module_permissions[]']").change(function () {
            var check_if_modulename = $(this).closest('tr').data('class');
            var checkbox = $(this).closest('tr').attr('class');
            var len = $("." + checkbox).find("input[type='checkbox']:checked").length;
            var module_name = $(this).closest('tr').data('header-id');
            //set all permissions under module name if it is checked
            if (check_if_modulename) {
                $("." + check_if_modulename).prop('checked', true);
            }
            if (this.checked) {
                if (len > 0) {
                    $("#" + module_name).prop('checked', true);
                }
            } else {
                var permissions = $(this).closest('tr').data("class");
                $("." + permissions).find("input[type='checkbox']").prop('checked', false);
                if (len == 0) {
                    $("#" + module_name).prop('checked', false);
                }
            }
        });
    });
</script>
@stop
