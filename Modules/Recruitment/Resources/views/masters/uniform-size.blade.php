@extends('adminlte::page')

@section('title', 'Uniform Sizes')

@section('content_header')
<h1>Uniform Sizes</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Uniform Sizes">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="uniform-size-table">
    <thead>
        <tr>
             <th></th>
            <th>#</th>
            <th>Size Name</th>
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
            {{ Form::open(array('url'=>'#','id'=>'size-name-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
             {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="size_name">
                    <label for="size_name" class="col-sm-3 control-label">Size Name
                        <span class="mandatory">*</span>
                    </label>
                    <div class="col-sm-9">
                        {{ Form::text('size_name',null,array('class'=>'form-control')) }}
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
        var table = $('#uniform-size-table').DataTable({
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
            ajax: "{{ route('recruitment.uniform-sizes.list') }}",
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
                {data: 'size_name', name: 'size_name'},
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


        /* Save Size Name - Start*/
        $('#size-name-form').submit(function (e) {
            e.preventDefault();
            if($('#size-name-form input[name="id"]').val()){
                var message = 'Size name has been updated successfully';
            }else{
                var message = 'Size name has been created successfully';
            }
            formSubmit($('#size-name-form'), "{{ route('recruitment.uniform-sizes.store') }}", table, e, message);
        });
        /* Save Size Name - End*/

         /* Editing Size Name - Start */
        $("#uniform-size-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.uniform-sizes.single",":id") }}';
            var url = url.replace(':id', id);
            $('#size-name-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="size_name"]').val(data.size_name);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Size Name: " + data.size_name)
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
        /* Editing Size Name - End */

         /* Deleting Size Name - Start */
        $('#uniform-size-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.uniform-sizes.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Size name has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Deleting Size Name - End */
    });
</script>
@stop
