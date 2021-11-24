@extends('adminlte::page')
@section('title', 'Uniform Measurement Points')
@section('content_header')
<h1>Uniform Measurement Points</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Uniform Measurement Points">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="uniform-measurement-points-table">
    <thead>
        <tr>
             <th></th>
            <th>#</th>
            <th>Name</th>
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
            {{ Form::open(array('url'=>'#','id'=>'uniform-measurement-points-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
             {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="name">
                    <label for="name" class="col-sm-3 control-label">Name
                        <span class="mandatory">*</span>
                    </label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control')) }}
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
@stop

@section('js')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#uniform-measurement-points-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.uniform-measurement-points.list') }}",
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
            columns: [
                {data: 'id', name: '',visible:false},
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'name', name: 'name'},
                {data: null,
                    orderable: false,
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


        /* Save Measurement Point Name - Start*/
        $('#uniform-measurement-points-form').submit(function (e) {
            e.preventDefault();
            if($('#uniform-measurement-points-form input[name="id"]').val()){
                var message = 'Measurement point name has been updated successfully';
            }else{
                var message = 'Measurement point name has been created successfully';
            }
            formSubmit($('#uniform-measurement-points-form'), "{{ route('recruitment.uniform-measurement-points.store') }}", table, e, message);
        });
        /* Save Measurement Point Name - End*/

         /* Editing Measurement Point Name - Start */
        $("#uniform-measurement-points-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.uniform-measurement-points.single",":id") }}';
            var url = url.replace(':id', id);
            $('#uniform-measurement-points-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Measurement Point Name: " + data.name)
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
        /* Editing Measurement Point Name - End */

         /* Deleting Measurement Point Name - Start */
        $('#uniform-measurement-points-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.uniform-measurement-points.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Measurement point name has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Deleting Measurement Point Name - End */
    });
</script>
@stop
