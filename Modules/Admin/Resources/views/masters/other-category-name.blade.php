{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Name')
@section('content_header')
<h1>  Other Subcategory</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Subcategory ">Add
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
                    <label for="document_name" class="col-sm-3 control-label">Enter  Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class' => 'form-control', 'Placeholder'=>'Other Subcategory', 'required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="document_type_id">
                    <label for="role_id" class="col-sm-3 control-label">Select Document Type</label>
                    <div class="col-sm-9">
                     {{Form::select('document_type_id',[''=>'Please Select']+$type_list,null, ['class' => 'form-control','id' => 'document_type_details','required'=>TRUE])}}
                     <small class="help-block"></small>
                 </div>
                </div>
                
                <div class="form-group" id="document_category_id">
                    <label for="role_id" class="col-sm-3 control-label">Select Category</label>
                    <div class="col-sm-9">
                     {{Form::select('other_category_lookup_id',[''=>'Please Select'],null, ['class' => 'form-control','id'=>'document_category_details','required'=>TRUE])}}
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
            ajax: "{{ route('other-category.list') }}",
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
                {data: 'document_type', name: 'document_type'},
                {data: 'category_name', name: 'category_name',defaultContent: "--"},
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
                    },
                }
                
            ]
        });
         } catch(e){
            console.log(e.stack);
        }
 /* Store Other category details*/
        $('#document_name_form').submit(function (e) {
            e.preventDefault();
            if($('#document_name_form input[name="id"]').val()){
                var message = 'Subcategory name has been updated successfully';
            }else{
                var message = 'Subcategory name has been created successfully';
            }
            formSubmit($('#document_name_form'), "{{ route('other-category.store') }}", table, e, message);
        });
function getCategories(document_type_id,document_category_id){

        var base_url = "{{route('other-category.categorylist', ':id')}}";
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
          $('#document_category_details').append("<option value="+value.id+">"+value.category_name+"</option>");

                               });
                $('#myModal select[name="other_category_lookup_id"] option[value="'+document_category_id
              +'"]').prop('selected',true);

               } else {
                  console.log(data);
                    }
          },
          })

             }
     /* Fetching the category details to category select box - start*/
    $('#document_type_details').on('change', function(e){
        var document_type_id = $(this).val();
        var document_category_id = null;
        getCategories(document_type_id,document_category_id); 

    });

 /* Edit the Other category details */
     $("#type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("other-category.single",":id") }}';
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
                        var document_category_id = data.other_category_lookup_id;
                        getCategories(document_type_id,document_category_id);                     
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal select[name="document_type_id"] option[value="'+data.document_type_id
+'"]').prop('selected',true);
                        $('#myModal select[name="document_category_id"] option[value="'+data.other_category_lookup_id
+'"]').prop('selected',true);
                       
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Name: "+ data.name)
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
         /* Delete records  in Othe category details*/
        $('#type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('other-category.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Subcategory name has been deleted successfully';
            deleteRecord(url, table, message);
        });

        
    });
</script>
@stop
