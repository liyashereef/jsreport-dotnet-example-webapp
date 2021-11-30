@extends('adminlte::page')

@section('title', 'Whistleblower Master')

@section('content_header')
<h1>Whistleblower Master</h1>
@stop
<style>
.control-label {
    padding-top: 7px;
    margin-bottom: 0;
    text-align: center;
}
#allocation-container{
    margin-top: 2em;
}
.init-status{
    margin-left: -4em;
}
</style>
@section('content')
<div class="row">
    <div class="col-md-12" id="allocation-container">
        <div class="form-group row">
            <div class="col-md-2">
                Set Initial status:
            </div>
            <div class="col-md-2 init-status">
                <select name="initialize-name" class="form-control" id="initialize-name">
                    <option value="" disabled selected>Select Any Name</option>
                    @foreach ($namesLookups as $key => $names)
                    <option value="{{$key}}">{{$names}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="button" class="button btn btn-primary blue" name="submit-intalize" id="submit-intalize" value="Set as Intial">
            </div>
        </div>
    </div>
</div>
<div class="add-new " data-title="Add New">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Status</th>
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
                <h4 class="modal-title" id="myModalLabel">Add New</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'type-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group row" id="name">
                    <label for="name" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-8">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder'=>'Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group row" id="status">
                    <label for="status" class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-8">
                        <select name="status" class="form-control">
                            <option value="" disabled selected>Select</option>
                            <option value="1">Open</option>
                            <option value="2">In Progress</option>
                            <option value="3">Closed</option>
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
<script>
    $(function () {
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
                        columns: [ 0,1, 2, 3]
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
                        emailContent(table, 'Banks');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('whistleblower-master.list') }}",
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
                {data: 'status', name: 'status'},

                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
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

        var data={!!json_encode($selectedInitialValue)!!};
        if(data){
            $('select[name="initialize-name"]').val(data[0].id)
        }

        /* Posting data to PositionLookupController - Start*/
        $('#type-form').submit(function (e) {
            e.preventDefault();
             if($('#type-form input[name="id"]').val()){
                var message = 'Whistleblower master has been updated successfully';
            }else{
                var message = 'Whistleblower master has been created successfully';
            }
            formSubmit($('#type-form'), "{{ route('whistleblower-master.store') }}", table, e, message);
        });


        $("#type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("whistleblower-master.single",":id") }}';
            var url = url.replace(':id', id);
            $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        console.log(data);
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal select[name="status"]').val(data.status)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Whistleblower Master")
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
                /* Editing Assignment Types - End */

        /* Deleting Whistleblower Master Types  - Start */
        $('#type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('whistleblower-master.destroy',':id') }}";
            var url = base_url.replace(':id', id);

            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action. Proceed?",
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
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        if (data.success) {
                        swal({
                                title: 'Success',
                                text: "Whistleblower master has been deleted successfully",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location.reload();
                            });

                    } else {
                        swal("Warning", data.message, "warning");
                    }
                    },
                    error: function (xhr, textStatus, thrownError) {
                    },
                    contentType: false,
                    processData: false,
                });
            });
        });
    });
    $('#submit-intalize').click(function(e){
        e.preventDefault();
        var initilalizeNameId = $('#initialize-name').val();
        var base_url = "{{ route('whistleblower-master.intial-status',':id') }}";
        var url = base_url.replace(':id', initilalizeNameId);
        if(initilalizeNameId != null){
        $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + initilalizeNameId,
                success: function (data) {
                    if (data.success) {
                        swal({
                                title: 'Success',
                                text: "Intial status updated",
                                type: "success",
                                icon: "success",
                                button: "Ok",

                            }, function () {
                                window.location.reload();
                            });

                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        }
    })
</script>
@stop

