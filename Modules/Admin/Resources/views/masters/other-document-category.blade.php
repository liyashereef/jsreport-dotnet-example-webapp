{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Name')
@section('content_header')
<h1> Document Names</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Name">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Document Type</th>
            <th>Document Category</th>
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
            {{ Form::open(array('url'=>'#','id'=>'document_name_form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="document_name" class="col-sm-3 control-label">Enter Document Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class' => 'form-control', 'Placeholder'=>'Document Name', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="document_type_id">
                    <label for="role_id" class="col-sm-3 control-label">Select Document Type<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                     {{Form::select('document_type_id',[''=>'Please Select']+$type_list,null, ['class' => 'form-control','id' => 'document_type_details'])}}
                     <small class="help-block"></small>
                 </div>
                </div>
                
                <div class="form-group" id="document_category_id">
                    <label for="role_id" class="col-sm-3 control-label">Document Category Type<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                     {{Form::select('document_category_id',[''=>'Please Select'],null, ['class' => 'form-control','id'=>'document_category_details'])}}
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
            ajax: "{{ route('other-document-category.list') }}",
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
                    data: 'document_name',
                    name: 'document_name'
                },
                {data: 'document_type', name: 'document_type'},
                {data: 'document_category', name: 'document_category'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                         actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id +'></a>'
                          @can('lookup-remove-entries')
                         actions +=  '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id +'></a>';
                          @endcan
                          return actions;
                    },
                }
                
            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Posting data to PositionLookupController - Start*/
        $('#document_name_form').submit(function (e) {
            e.preventDefault();
            if($('#document_name_form input[name="id"]').val()){
                var message = 'Document name has been updated successfully';
            }else{
                var message = 'Document name has been created successfully';
            }
            formSubmit($('#document_name_form'), "{{ route('document-name-details.store') }}", table, e, message);
        });
    /* Fetching the category details to category select box - start*/
     function getCategories(document_type_id,document_category_id){
        
         var base_url = "{{route('documentcategory.list', ':id')}}";
        var url = base_url.replace(':id', document_type_id);
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            data: {'id':document_type_id},
            success: function (data) {
                if (data) {
                  $('#document_category_details').find('option').not(':first').remove();
                  $.each(data, function(key, value){
                    $('#document_category_details').append("<option value="+value.id+">"+value.document_category+"</option>");
                     
                  });
                  $('#myModal select[name="document_category_id"] option[value="'+document_category_id
+'"]').prop('selected',true);
                } else {
                    console.log(data);
                }
            },
        })

     }
     /* Fetching the category details to category select box - end*/

     /* On changing projects document name will be listed - Start */

     $('#document_type_details').on('change', function(e){
        var document_type_id = $(this).val();
        var document_category_id = null;
        getCategories(document_type_id,document_category_id);

    });
    /* On changing projects document name will be listed - End */


        $("#type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("document-name-details.single",":id") }}';
            var url = url.replace(':id', id);
            $('#document_name_form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        var document_type_id = data.document_type_id;
                        var document_category_id = data.document_category_id;
                        getCategories(document_type_id,document_category_id);
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal select[name="document_type_id"] option[value="'+data.document_type_id
+'"]').prop('selected',true);
                        $('#myModal select[name="document_category_id"] option[value="'+data.document_category_id
+'"]').prop('selected',true);
                       
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Reason: "+ data.reason)
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
            var base_url = "{{ route('document-name-detail.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Document name has been deleted successfully';
            deleteRecord(url, table, message);
        });

        
    });
</script>
@stop
