@extends('layouts.app')
@section('css')
<style>
    #table-id .fa {
        margin-left: 11px;
    }
    table.dataTable tbody th,
    table.dataTable tbody td {
    padding: 8px 18px;
    }
    .add-new{
    margin-top: 0px;
    margin-bottom: 10px;
    }

    
</style>
@stop
@section('content')
<div class="table_title">
    <h4> Customer Key Summary </h4>
</div>
@can(['add_edit_keys'])  
<div class="add-new" data-title="Add Key">Add
    <span class="add-new-label">New</span>
</div>
@endcan
<div class="sub_table_title" id="doc_sub_table"></div>  
<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
             <th class="sorting" width="2%">#</th>
             <th class="sorting" width="10%">Key ID</th>
             <th class="sorting" width="10%">Key Name</th>
             <th class="sorting" width="10%">Key Image</th>
             <th class="sorting" width="10%">Checked Out To</th>
             <th class="sorting" width="10%">Company Name</th>
             <th class="sorting" width="10%">Checked In By</th>
             <th class="sorting" width="10%">Key Status</th>
             @canany(['add_edit_keys','delete_keys'])
             <th class="sorting" width="2%">Action</th> 
             @endcan
         </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Add Key Form</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::open(array('url'=>'#','id'=>'customerkey-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ Form::hidden('id', null) }}
        {{ Form::hidden('customer_id', isset($id) ? old('customer_id',$id) : null,array('id'=>'customer_id')) }}
        <div class="modal-body">
                <div class="form-group" id="key_id">
                    <label for="key_id" class="col-sm-3 control-label" style="text-align: left;">Key ID</label>
                    <div class="col-sm-9">
                      {{ Form::text('key_id',null,array('class'=>'form-control','placeholder' => 'Key ID','maxlength'=>100)) }}
                      <small class="help-block"></small>
                  </div>
                </div>
                <div id="room_name" class="form-group">
                    <label for="room_name" class="col-sm-3 control-label" style="text-align: left;">Room Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('room_name',null,array('class'=>'form-control','placeholder' => 'Room Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="key_image">
                <label for="key_image" class="col-sm-3 control-label" style="text-align: left;">Key Image</label>
                    <div class="col-sm-9">
                    <input type="file" class="form-control" name="key_image" id="key_image_id">
                        <label id="key_image_name" ></label><br/>
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
@include('keymanagement::key-setting.partials.modal')
@stop 
@section('scripts')
<script>
    $(function () {
        $('.select2').select2();
        var _URL = window.URL || window.webkitURL;
        var id = $('#customer_id').val();
        var url = '{{ route('keysetting.list',[":id"]) }}';
        url = url.replace(':id', id);
        

        /* Datatable- Start */

        var table = $('#table-id').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: url,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)', 
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {
                    data: 'key_id',
                    name: 'key_id',
                    defaultContent: "--"
                },
                {
                    data: 'key_name',
                    name: 'key_name',
                    defaultContent: "--",
                },
                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        actions = '';
                        var actions = '';
                        var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                        view_url = view_url.replace(':id', o.attachment_id);
                        view_url = view_url.replace(':module', 'keymanagement-key-image');
                        if (o.attachment_id) {
                        actions += '<a title="Download"  target="_blank"  href="' + view_url + '"><i class="fa fa-lg fa-picture-o"></i></a>';
                        } else {
                        actions +='--';
                        }
                        return actions;
                    },

                }, 
                {
                    data: 'checked_out_to', 
                    name: 'checked_out_to',
                    defaultContent: "--"
                   
                }, 
                {
                    data: 'key_checked_company_name', 
                    name: 'key_checked_company_name',
                    defaultContent: "--"
                   
                },
                {
                    data: 'checked_in_by', 
                    name: 'checked_in_by',
                    defaultContent: "--"
                   
                },
                {
                    data: 'key_availability', 
                    name: 'key_availability',
                    defaultContent: "--"
                   
                },
            @canany(['add_edit_keys','delete_keys'])
                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('add_edit_keys')
                        actions = '<a href="#" class="edit fa fa-edit" data-id=' + o.id + ' ></a>';
                        @endcan
                        @can('delete_keys')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions; 
                    },
                }
            @endcan 
            ]
            
        });
         /* Datatable- End */
 

        /*Add new - modal popup - Start */

        $('.add-new').on('click', function () {
            var title = $(this).data('title');
            $("#myModal").modal();
            $('#myModal form').trigger('reset');
            $('#myModal .modal-title').text(title);
            $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $('#myModal form').find('#key_image_name').html('');
            $('#myModal form').find("input[name='id']").val('');
        });

        /*Add new - modal popup - End */

        $('#customerkey-form').submit(function (e) {
            e.preventDefault();
            
            var $form = $(this);
            url = "{{ route('keysetting.store') }}";
            var formData = new FormData($('#customerkey-form')[0]);
            // $('#myModal form').find('#key_image_name').html('');
            $('#severity-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                $("#myModal").modal('hide');
                if (data.success) {
                    if(data.result){
                        swal({
                        title: "Saved",
                        text: "Key details updated successfully",
                        type: "success"
                    },function(){
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        $('#customerkey-form')[0].reset();
                        table.ajax.reload();
                    });
                    } else {
                        swal({
                        title: "Saved",
                        text: "New key details added",
                        type: "success"
                    },function(){
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        $('#customerkey-form')[0].reset();
                        table.ajax.reload();
                    });
                        
                    }
               
                } else {
                    console.log(data);
                    swal("Oops", "The record has not been saved", "warning");
                }
                },
                fail: function (response) {
                console.log(response);
                swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
                if(typeof(xhr.responseJSON.errors.key_image) != "undefined" && xhr.responseJSON.errors.key_image !== null) {
                    if(xhr.responseJSON.errors.key_image.length > 0){
                    $('#key_image_id').val('');
                }
                }
                },
                contentType: false,
                processData: false,
            });
            });


            $('#table-id').on('click', '.edit', function(e){
                var id = $(this).data('id');
                var base_url = "{{route('keysetting.single', ':id')}}";
                var url = base_url.replace(':id', id);
                console.log(id,url);
                $("#key_image_id").val(null);
                $('#customerkey-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                        '');
    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:url,
                    type: 'GET',
                    success: function (data) {
                    if(data){
                        $('input[name="id"]').val(data.id);
                        $('input[name="key_id"]').val(data.key_id);
                        $('input[name="room_name"]').val(data.room_name);
                        if(data.attachment){
                            $('#key_image').val(data.attachment.original_name);
                            $('#key_image_name').html(data.attachment.original_name);
                        }else{
                            $('#key_image_name').html('');
                        }
                        
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Key : ")
                    }
                    },
                    fail: function (response) {
                        swal("Oops", "Something went wrong", "warning");
                    },
                    contentType: false,
                    processData: false,
                });
                });

        /***** Key SUmmary  Delete - Start */
        $('#table-id').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('keysetting.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action.Proceed?",
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
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal("Deleted", "Record has been deleted successfully", "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else {
                                swal("Alert", "Cannot able to delete ", "warning");
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

    });

    $(document).keyup(function(e) {jQuery       
         if (e.key === "Escape") { 
          $("#myModal").modal('hide');
       }
     });

     
    function showFileSize(file_name) {
        var input, file;
        var file_size = 0;
        if (!window.FileReader) {
            console.log("The file API isn't supported on this browser yet.");
            return;
        }
        input = $(file_name);
        if(input[0].files.length > 0){
            file_size = (input[0].files[0].size)/(1000000);
        }
        return file_size;
    }


    





</script>


@stop


