{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Name')
@section('content_header')
<h1> Document Category</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New  Document Category">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Document Category</th>
            <th>Document Type</th>
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
            {{ Form::open(array('url'=>'#','id'=>'document_category_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="document_category">
                    <label for="document_category" class="col-sm-3 control-label">Enter Category</label>
                    <div class="col-sm-9">
                        {{ Form::text('document_category',null,array('class' => 'form-control', 'Placeholder'=>'Document Category', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="document_type_id">
                    <label for="role_id" class="col-sm-3 control-label">Select Document Type</label>
                    <div class="col-sm-9">
                     {{Form::select('document_type_id',[''=>'Please Select']+$type_list,null, ['class' => 'form-control','id' => 'document_type_details','required'=>TRUE])}}
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
            ajax: "{{ route('document-category.list') }}",
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
                    data: 'document_category',
                    name: 'document_category'
                },
                {data: 'document_type', name: 'document_type'},

                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        if (o.is_editable == false) {
                            var actions = '';
                            @can('edit_masters')
                            actions += '<a class="{{Config::get('globals.editFontIcon')}} edit-disable" title="Unable to edit"></a>'
                            @endcan
                            @can('lookup-remove-entries')
                                actions +=  '<a class="{{Config::get('globals.deleteFontIcon')}} edit-disable"></a>';
                             @endcan
                          return actions;
                        } else {
                             var actions = '';
                            @can('edit_masters')
                            actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id +'></a>'
                            @endcan
                            @can('lookup-remove-entries')
                                actions +=  '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id +'></a>';
                             @endcan
                          return actions;
                            }
                    }
                    },


            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Posting data to PositionLookupController - Start*/
        $('#document_category_form').submit(function (e) {
            e.preventDefault();
            if($('#document_category_form input[name="id"]').val()){
                var message = 'Document category has been updated successfully';
            }else{
                var message = 'Document category has been created successfully';
            }
            formSubmit($('#document_category_form'), "{{ route('document-category.store') }}", table, e, message);
        });


        $("#type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("document-category.single",":id") }}';
            var url = url.replace(':id', id);
            $('#document_category_form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="document_category"]').val(data.document_category);
                        $('#myModal select[name="document_type_id"] option[value="'+data.document_type_id
+'"]').prop('selected',true);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Category: "+ data.document_category)
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
            var base_url = "{{ route('document-category.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Document category has been deleted successfully';
            deleteRecord(url, table, message);
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
