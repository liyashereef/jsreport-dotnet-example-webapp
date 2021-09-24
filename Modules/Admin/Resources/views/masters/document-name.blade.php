{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Name')
@section('content_header')
<h1> Document Names</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New  Document Name">Add
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
                    <label for="role_id" class="col-sm-3 control-label">Select Document Type</label>
                    <div class="col-sm-9">
                     {{Form::select('document_type_id',[''=>'Please Select']+$type_list,null, ['class' => 'form-control','id' => 'document_type_details','required'=>TRUE])}}
                     <small class="help-block"></small>
                 </div>
                </div>
                <div class="form-group" id="document_category_id">
                    <label for="role_id" class="col-sm-3 control-label">Document Category</label>
                    <div class="col-sm-9">
                     {{Form::select('document_category_id',[''=>'Please Select'],null, ['class' => 'form-control','id'=>'document_category_details','required'=>TRUE])}}
                     <small class="help-block"></small>
                 </div>
                </div>

                <div class="form-group" id="is_auto_archive">
                    <label for="auto_archive" class="col-sm-3 control-label">Auto-Archive Old Document</label>
                    <div class="col-sm-9">
                        <input type="checkbox" id="is_auto_archive" class="chk" name="is_auto_archive" value="1"  checked="checked">
                     <small class="help-block"></small>
                 </div>
                </div>
                <div class="form-group" id="permissions">
                    <label for="permissions" class="col-sm-3 control-label">Authorized Access</label>
                    <div class="col-sm-9">

                        <select class="form-control col-sm-9" placeholder="Please Select" name="authorized_access[]" id="authorized_access" multiple="multiple">
                            @foreach ($authorized_access_list as $key => $permission)
                                <option value="{{$key}}">{{$permission}}</option>
                            @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="other_category_name_id">
                    <label for="role_id" class="col-sm-3 control-label">Document Subcategory</label>
                    <div class="col-sm-9">
                     {{Form::select('other_category_name_id',[''=>'Please Select'],null, ['class' => 'form-control','id'=>'other_category_name_details'])}}
                     <small class="help-block"></small>
                 </div>
                </div>
                <div class="form-group" id="is_valid">
                    <label for="role_id" class="col-sm-3 control-label">Is valid</label>
                    <div class="col-sm-9">
                        <input type="checkbox" id="is_valid" class="chk" name="is_valid" value="1"  checked="checked">
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

        $('#authorized_access').select2({
            //multiple: true,
            width: '100%',
          //  placeholder: 'Please select'
        });

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
            ajax: "{{ route('document-name.list') }}",
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
                    name: 'document_name',
                    defaultContent: "--",
                },


                {data: 'document_type', name: 'document_type',defaultContent: "--",},
                {data: 'document_category', name: 'document_category',defaultContent: "--",},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        if (o.is_editable == false) {
                            var actions = '';
                            @can('edit_masters')
                            actions += '<a class="fa fa-pencil edit-disable" title="Unable to edit"></a>'
                            @endcan
                            @can('lookup-remove-entries')
                                actions +=  '<a class="fa fa-trash-o edit-disable"></a>';
                             @endcan
                          return actions;
                        } else {
                             var actions = '';
                            @can('edit_masters')
                            actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id +'></a>'
                            @endcan
                            @can('lookup-remove-entries')
                                actions +=  '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id +'></a>';
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

        $('#document_name_form').submit(function (e) {
            e.preventDefault();
            if($('#document_name_form input[name="id"]').val()){
                var message = 'Document name has been updated successfully';
            }else{
                var message = 'Document name has been created successfully';
            }
            formSubmit($('#document_name_form'), "{{ route('document-name.store') }}", table, e, message);
        });

    /* Fetching the category details to category select box - start*/

     function getCategories(document_type_id,document_category_id){

         var base_url = "{{route('document-name.categorylist', ':id')}}";
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
                    if(document_type_id == {{ OTHER }}){
                        $('#other_category_name_id').show();
                        $('#is_valid').show();
                        $('#document_category_details').append("<option value="+value.id+">"+value.category_name+"</option>");
                    }else{
                        $('#other_category_name_id').hide();
                        $('#is_valid').hide();
                        $('#document_category_details').append("<option value="+value.id+">"+value.document_category+"</option>");
                    }
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
            $('#authorized_access').val("").trigger('change');
            var id = $(this).data('id');
            var url = '{{ route("document-name.single",":id") }}';
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
                        var other_category_name_id = data.other_category_name_id;
                        var is_valid = data.is_valid;
                        var is_auto_archive = data.is_auto_archive
                        getCategories(document_type_id,document_category_id);
                        otherCategoryNames(document_category_id,other_category_name_id);
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal select[name="document_type_id"] option[value="'+data.document_type_id
+'"]').prop('selected',true);
                        $('#myModal select[name="document_category_id"] option[value="'+data.document_category_id
+'"]').prop('selected',true);
                        if(is_valid == null){
                            $("#myModal input[type='checkbox']").prop('checked', false);
                        }else{
                            $("#myModal input[type='checkbox']").prop('checked', true);
                        }

                        if(is_auto_archive == 0){
                            $("#myModal input[type='checkbox']").prop('checked', false);
                        }else{
                            $("#myModal input[type='checkbox']").prop('checked', true);
                        }
                        /***Setting selcted values in select2 mutiple - Begin ****/
                            var authorized_access = [];
                           if(data.authorized_access_details)
                            {
                                $.each(data.authorized_access_details,function(key , item){
                                    authorized_access.push(item.access_id);
                                });
                            }
                            if(authorized_access.length != 0)
                            {
                                $('#authorized_access').val(authorized_access).trigger('change');
                            }


                       /***Setting selcted values in select2 mutiple - End ****/
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

     /* On changing other document name will be listed - Start */

     $('#document_category_details').on('change', function(e){
        var document_category_id = $(this).val();
        var other_category_name_id = null;
        otherCategoryNames(document_category_id,other_category_name_id);


    });
    /* On changing other document name will be listed - End */

    /* Fetching the category details to category select box - start*/

     function otherCategoryNames(document_category_id,other_category_name_id){

        var base_url = "{{route('document-name.othercategorynames', ':id')}}";
        var url = base_url.replace(':id', document_category_id);

       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           url: url,
           type: 'GET',
           data: {'id':document_category_id},
           success: function (data) {
               if (data) {

                 $('#other_category_name_details').find('option').not(':first').remove();
                 $.each(data, function(key, value){
                    $('#other_category_name_details').append("<option value="+value.id+">"+value.name+"</option>");
                 });
                 $('#myModal select[name="other_category_name_id"] option[value="'+other_category_name_id
+'"]').prop('selected',true);
               } else {
                   console.log(data);
               }
           },
       })

    }
    /* Fetching the category details to category select box - end*/


        $('#type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('document-name-detail.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Document name has been deleted successfully';
            deleteRecord(url, table, message);
        });

        $('#other_category_name_id').hide();
        $('#is_valid').hide();




    });
</script>
@stop
