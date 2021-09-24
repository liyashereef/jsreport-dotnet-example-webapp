{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Name')
@section('content_header')
<h1>Cost Center</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Cost Center">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Number</th>
            <th>Owner</th>
            <th>Senior Manager</th>
            <th>Region</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'course_center_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="center_number">
                    <label for="center_number" class="col-sm-3 control-label">Center Number</label>
                    <div class="col-sm-9">
                        {{ Form::text('center_number',null,array('class' => 'form-control', 'Placeholder'=>'Center Number', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="center_owner_id">
                    <label for="role_id" class="col-sm-3 control-label">Select Owner</label>
                    <div class="col-sm-9">
                     {{Form::select('center_owner_id',@$lookups['user_lookups'][0],null, ['class' => 'form-control select2','id' => 'center_owner_id', 'style'=>"width: 100%;",'required'=>TRUE])}}
                     <small class="help-block"></small>
                 </div>
                </div>
                <div class="form-group" id="center_senior_manager_id">
                    <label for="center_senior_manager_id" class="col-sm-3 control-label">Select Senior Manager</label>
                    <div class="col-sm-9">
                     {{Form::select('center_senior_manager_id',@$lookups['user_lookups'][0],null, ['class' => 'form-control select2','id' => 'center_senior_manager_id', 'style'=>"width: 100%;",'required'=>TRUE])}}
                     <small class="help-block"></small>
                 </div>
                </div>
                <div class="form-group" id="region_id">
                    <label for="region_id" class="col-sm-3 control-label">Region</label>
                    <div class="col-sm-9">
                     {{Form::select('region_id',@$lookups['regions'][0],null, ['class' => 'form-control','id' => 'region_id','required'=>TRUE])}}
                     <small class="help-block"></small>
                 </div>
                </div>
                <div class="form-group" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description',null,array('class' => 'form-control', 'Placeholder'=>'Description')) }}
                        <small class="help-block"></small>
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
        $('.select2').select2();
            $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#type-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3]
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
            ajax: "{{ route('cost-center.list') }}",
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
                    data: 'center_number',
                    name: 'center_number'
                },
                {
                    data: 'center_owner',
                    name: 'center_owner'
                },
                {
                    data: 'center_senior_manager',
                    name: 'center_senior_manager'
                },
                {
                    data: 'region',
                    name: 'region'
                },


                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                         actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id +'></a>'
                         @endcan
                          @can('lookup-remove-entries')
                         actions +=  '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id +'></a>';
                          @endcan
                          return actions;
                    }
                    },


            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Posting data to PositionLookupController - Start*/
        $('#course_center_form').submit(function (e) {
            e.preventDefault();
            if($('#course_center_form input[name="id"]').val()){
                var message = 'Cost center has been updated successfully';
            }else{
                var message = 'Cost center has been created successfully';
            }
            formSubmit($('#course_center_form'), "{{ route('cost-center.store') }}", table, e, message);
        });

        $('select#region_id').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Please Select',
            width: '100%'
            });
        $("#type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("cost-center.single",":id") }}';
            var url = url.replace(':id', id);
            $('#course_center_form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
                $('#course_center_form').trigger("reset");
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="center_number"]').val(data.center_number);
                        $('#myModal select[name="center_owner_id"] option[value="'+data.center_owner_id
+'"]').prop('selected',true);
                        $('#myModal select[name="center_senior_manager_id"] option[value="'+data.center_senior_manager_id
+'"]').prop('selected',true);
                        $('#myModal select[name="region_id"] option[value="'+data.region_id
+'"]').prop('selected',true);
                        $('#myModal textarea[name="description"]').text(data.description);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Center: "+ data.center_number)
                        $(".select2").select2();
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

        $('#type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('cost-center.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Cost center has been deleted successfully';
            deleteRecord(url, table, message);
        });


    });

    $('#myModal').on('hidden.bs.modal', function() {
                        $('#myModal input[name="center_number"]').val('');
                        $('#myModal select[id="center_owner_id"] option[value=""]').prop('selected',true);
                        $('#myModal select[id="center_senior_manager_id"] option[value=""]').prop('selected',true);
                        $('#myModal select[name="region_id"] option[value="0"]').prop('selected',true);
                        $('#myModal textarea[name="description"]').text('');
                        $(".select2").select2();
    });
</script>
@stop
