@extends('adminlte::page')
@section('title', 'Department Master')
@section('content_header')
<h1>Department Master</h1>
@stop
@section('content')
<style>
.employee-link{
    margin-left: 1em;
    margin-right: 1em;
}

</style>
<style>
element.style {
    width: 300px;
}
</style>
<div class="add-new" data-title="Add New Department">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="department-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Department Name</th>
            <th>Regional Manager Allocated</th>
            <th>Supervisor Allocated</th>
            <th>Allocated Employees</th>
            <th>Actions</th>
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
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'department_master_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="name" class="col-sm-3 ">Department Name <span class="mandatory">*</span></label>
                    <div class="col-sm-8">
                        {{ Form::text('name',null,array('class' => 'form-control', 'Placeholder'=>'Department Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div  class="form-group row" id="employee">
                <label class="employee col-sm-3">  Send Notification to </label>
                <div class="col-sm-8" >
                    <select name="employee[]"  class="form-control select2 employee_select" id="employee_id" style="width: 525px;" multiple>
                    @foreach($users as $each_userlist)
                    <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
                    </option>
                    @endforeach
                </select>
                <span class="help-block"></span>
                </div>
                </div>

                <div  class="form-group row" >
                    <label class="col-form-label col-md-3">   </label>
                    <div class="col-md-4" style="padding-left: 30px;">
                    <input type="checkbox"  name="allocated_regionalmanager" value="1" id="allocated_regionalmanager" style="margin-left: -1em;">
                    <label for="allocated_regionalmanager">Select allocated Area Managers</label>
                    <span class="help-block"></span>
                    </div>
                    <div class="col-md-4" style="padding-left: 30px;">
                    <input type="checkbox"  name="allocated_supervisor" value="1" id="allocated_supervisor" style="margin-left: -1em;">
                    <label for="allocated_supervisor">Select allocated Supervisors</label>
                    <span class="help-block"></span>
                    </div>
                </div>

       <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
        {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
    </div>
    {{ Form::close() }}
</div>
</div>
</div>
</div>
@stop @section('js')
<script>
    $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#department-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3, 4]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Positions');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('department-master.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
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
                    data: 'allocated_regionalmanager',
                    name: 'allocated_regionalmanager'
                },
                {
                    data: 'allocated_supervisor',
                    name: 'allocated_supervisor'
                },
                {
                    data: 'emp_allocation',
                    name: 'emp_allocation',
                    defaultContent:'--',
                    orderable:false
                 },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan

                        return actions;
                    },
                }


            ]
        });
         } catch(e){
            console.log(e.stack);
        }


            $(".select").select2();
            $('#employee_id').select2({
                id: '-1',
            });

            $('.add-new').click(function () {
                $('#myModal select[name="employee[]"] ').val('');
                $('#myModal input[name="id"]').val('');
                $('#myModal input[name="name"]').val('');
                $('#myModal select[name="employee[]"]').prop('selected',false);
                $('.employee_select').val('').trigger('change');
                $('#myModal select[name="employee[]"]').prop('disabled',false);
                $("#allocated_regionalmanager").prop("checked", false);
                $("#allocated_supervisor").prop("checked", false);
            });


        /* Posting data to PositionLookupController - Start*/
        $('#department_master_form').submit(function (e) {
            e.preventDefault();
            if($('#department_master_form input[name="id"]').val()){
                var message = 'Department master has been updated successfully';
            }else{
                var message = 'Department master has been created successfully';
            }
            formSubmit($('#department_master_form'), "{{ route('department-master.store') }}", table, e, message);
        });

        $("#department-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("department-master.single",":id") }}';
            var url = url.replace(':id', id);
            $('#department_master_form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        if(data.allocated_regionalmanager){
                            $("#allocated_regionalmanager").prop("checked", true);
                        }else{
                            $("#allocated_regionalmanager").prop("checked", false);
                        }
                        if(data.allocated_supervisor){
                            $("#allocated_supervisor").prop("checked", true);
                        }else{
                            $("#allocated_supervisor").prop("checked", false);
                        }
                        $('#myModal select[name="employee[]"] ').val('');
                        if(data.employee_mapping!=null && data.employee_mapping.length>0)
                        {
                            $.each(data.employee_mapping, function(key, value) {

                            $('#myModal select[name="employee[]"]  option[value="'+value.user_id+'"]').prop("selected", true).change();
                            });
                        }
                        $(".select2").select2()
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Department Master")
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

        $('#department-table').on('click', '.delete', function (e) {
            var table = $('#department-table').DataTable();
            var id = $(this).data('id');
            var base_url = "{{ route('department-master.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Department master has been deleted successfully';
            deleteRecord(url, table, message);
        });

        $("#employee_id").change(function() {
        var selected=[];
        jQuery.each($(this).val(), function(index,value){
            selected.push(parseInt(value));
            });

        });


    });
</script>
<style>
 a.disabled {
    pointer-events: none;
    cursor: default;
}
</style>
@stop
