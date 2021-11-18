@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Color settings')
@section('content_header')

<h1>Color Settings</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Color settings">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Group</th>
            <th>Color Code</th>
            <th>Range From</th>
            <th>Range Till</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="lineofbusinesstitle">
                    <label for="Title" class="col-sm-3 control-label">Title</label>
                    <div class="col-sm-9">
                        {{ Form::text('Title',null,array('class' => 'form-control', 'Placeholder'=>'Title', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                    <label for="Group" class="col-sm-3 control-label">Group</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="Group" name="Group" required>
                            <option value="0">Select any</option>
                            <option value="1">Temperature</option>
                            <option value="2">Age </option>
                        </select>
                        <small class="help-block"></small>
                    </div>
                    <label for="colorcode" class="col-sm-3 control-label">Color Code</label>
                    <div class="col-sm-9">
                        {{ Form::text('colorcode',null,array('class' => 'form-control', 'Placeholder'=>'Color Code', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                    <label for="rangefrom" class="col-sm-3 control-label">Range From</label>
                    <div class="col-sm-9">
                        {{ Form::text('rangefrom',null,array('class' => 'form-control num', 'Placeholder'=>'Range From', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                    <label for="rangetill" class="col-sm-3 control-label">Range Till</label>
                    <div class="col-sm-9">
                        {{ Form::text('rangetill',null,array('class' => 'form-control num', 'Placeholder'=>'Range Till', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
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
@stop @section('js')
<script>
    $(function () {
          $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#table').DataTable({
               dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4,5]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4,5]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1,2,3,4,5]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Course Category');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('admin.colorsettings') }}",
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
                    sortable:false
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data:null,
                    render:function(o){
                        if(o.fieldidentifier=="2"){
                            return "Age ";
                        }
                        else if(o.fieldidentifier=="1"){
                            return "Temperature ";
                        }
                    }
                },
                {
                    data: 'colorhexacode',
                    name: 'Color code'
                },
                {
                    data: 'rangebegin',
                    name: 'Range from'
                },
                {
                    data: 'rangeend',
                    name: 'Range till'
                },
                {
                    data: 'created_at',
                    name: 'Created at'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                       
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                       
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Course Category Save - Start */
        $('#form').submit(function (e) {
            e.preventDefault();
            if($('#form input[name="id"]').val()){
                var message = 'Color group has been updated successfully';
            }else{
                var message = 'Color group has been created successfully';
            }
            formSubmit($('#form'), "{{ route('admin.colorsettings.store') }}", table, e, message);
            table.ajax.reload();


            $("#myModal").modal("hide");
            

        });
        /* Course Category Save- End */

        /* Course Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("admin.colorsettings.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {                      
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="Title"]').val(data.title)
                       // $('#myModal input[name="Group"]').val(data.FieldIdentifier)
                       $('#Group option:eq("'+data.fieldidentifier+'")').prop('selected', true)

                        $('#myModal input[name="colorcode"]').val(data.colorhexacode)
                        $('#myModal input[name="rangefrom"]').val(data.rangebegin)
                        $('#myModal input[name="rangetill"]').val(data.rangeend)


                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Color setting: "+ data.title)
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
        /* Course Category Edit - End */

        /* Course Category Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('admin.colorsettings.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action! Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            if (data.success) {
                                swal("Deleted", "Color group has been deleted successfully", "success");
                                table.ajax.reload();

                                if (table != null) {
                                }
                            } else {
                                swal("Alert", "Color group has one or more group", "warning");
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
                });
        /* Course Category Delete- End */
    });
</script>
@stop
