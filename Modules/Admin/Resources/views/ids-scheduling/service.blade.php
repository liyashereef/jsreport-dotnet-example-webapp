@extends('adminlte::page')
@section('title', 'IDS Services')
@section('content_header')
<h1>IDS Services</h1>
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }
    .select2 .select2-container{
        width : 12% !important;
    }
    .display-hide{
        display: none;
    }
</style>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" onclick="addnew()" data-title="Add New Services">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="service-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Short Name</th>
            <th>Rate</th>
            <th>Office</th>
            <th>Photo Service</th>
            <th>Photo Service Required</th>
            <th>Description</th>
            <th>Created Date</th>
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
                <h4 class="modal-title" id="myModalLabel">Services</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'ids-service-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group row" id="name">
                    <label for="name" class="col-sm-4 control-label">Name</label>
                    <div class="col-sm-8">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Service Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="short_name">
                    <label for="short_name" class="col-sm-4 control-label">Short Name</label>
                    <div class="col-sm-8">
                        {{ Form::text('short_name',null,array('class'=>'form-control','placeholder' => 'Short Service Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="rate" class="form-group row">
                    <label for="rate" class="col-sm-4 control-label">Rate</label>
                    <div class="col-sm-8">
                        {{ Form::number('rate',null,array('class'=>'form-control','placeholder' => 'Rate','step'=>"0.01", 'title'=>"Currency",'pattern'=>"^\d+(?:\.\d{1,2})?$")) }}
                        <small class="help-block"></small>
                    </div>
                </div>


                <div class="form-group row" id="office_ids">
                    <label for="office_ids" class="col-sm-4 control-label">Select Office </label>
                    <div class="col-sm-8">
                        {!!Form::select('office_ids[]',$office,null, ['class' => 'form-control','id'=>'office-ids','multiple'=>"multiple",'style'=>'width: 524px;'])!!}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="tax_master_id">
                    <label for="tax_master_id" class="col-sm-4 control-label">Select Tax Master </label>
                    <div class="col-sm-8">
                        {!!Form::select('tax_master_id',['Please Select']+$taxes,null, ['class' => 'form-control','id'=>'taxMasterId','style'=>'width: 524px;'])!!}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="description">
                    <label for="description" class="col-sm-4 control-label">Description</label>
                    <div class="col-sm-8">
                        {{ Form::textarea('description',null,['class' => 'form-control','id'=>'note','rows'=>'3']) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <!-- Photo Service - Start -->
                <div class="form-group row" id="is_photo_service">
                    <label for="is_photo_service" class="col-sm-4 control-label">Enable Photo Service </label>
                    <div class="col-sm-8">
                        <label class="switch" style="">
                            <input name="is_photo_service" type="checkbox" value="1" id="isPhotoService">
                            <span class="slider round"></span>
                        </label>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row display-hide" id="is_photo_service_required">
                    <label for="is_photo_service_required" class="col-sm-4 control-label">Enable Photo Service Required</label>
                    <div class="col-sm-8">
                        <label class="switch" style="">
                            <input id="isPhotoServiceRequired" name="is_photo_service_required" type="checkbox" value="1">
                            <span class="slider round"></span>
                        </label>
                        <small class="help-block"></small>
                    </div>
                </div>

                <!-- Photo Service - End -->

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
        $('#office-ids').select2();//Added Select2 to office-ids listing
        $('#taxMasterId').select2();
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#service-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function (e, dt, node, conf) {
                            emailContent(table, 'Services');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('idsServices.getAll') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [[ 5, "desc" ]],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data:'short_name',
                        name:'short_name'
                    },
                    {
                        data: 'rate',
                        name: 'rate'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                           var office = "";
                           if(o.ids_office_service_allocation){

                                $.each(o.ids_office_service_allocation, function(key,value){
                                    office += value.ids_office.name+' , ';
                                });
                           }

                            return office.replace(/,\s*$/, "");
                        },
                    },
                    {
                        name: 'is_photo_service',
                        data: null,
                        orderable: true,
                        render: function (o) {
                            var actions = "";
                            if(o.is_photo_service == 1){
                                actions = 'Yes';
                            }else{
                                actions = 'No';
                            }
                            return actions;
                        }
                    },
                    {
                        name: 'is_photo_service_required',
                        data: null,
                        orderable: true,
                        render: function (o) {
                            var actions = "";
                            if(o.is_photo_service_required == 1){
                                actions = 'Yes';
                            }else{
                                actions = 'No';
                            }
                            return actions;
                        }
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                           var actions = "";
                                @can('edit_masters')
                                    actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                                @endcan
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' +o.id + '></a>';
                                @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        $('#isPhotoService').on("click", function (e){
            if($('#isPhotoService').is(':checked') == true){
                $('#is_photo_service_required').removeClass('display-hide')
            }else{
                $('#is_photo_service_required').addClass('display-hide');
                $('#isPhotoServiceRequired').prop('checked', false);
            }
        });

        /* Service Store - Start*/
        $('#ids-service-form').submit(function (e) {
            e.preventDefault();
            if($('#ids-service-form input[name="id"]').val()){
                var message = 'Service has been updated successfully';
            }else{
                var message = 'Service has been created successfully';
            }
            formSubmit($('#ids-service-form'), "{{ route('idsServices.store') }}", table, e, message);
        });
        /* Service Store - End*/

        /* Service Edit - Start*/
        $("#service-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("idsServices.single",":id") }}';
            var url = url.replace(':id', id);
            $('#ids-service-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        console.log('data', data);
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal input[name="short_name"]').val(data.short_name);
                        $('#myModal input[name="rate"]').val(data.rate);
                        $('#myModal textarea[name="description"]').val('');
                        $('#myModal textarea[name="description"]').val(data.description);
                        $('#myModal input[name="is_photo_service"]').prop('checked', data.is_photo_service);
                        $('#myModal input[name="is_photo_service_required"]').prop('checked', data.is_photo_service_required);
                        if(data.is_photo_service == 1){
                            $('#is_photo_service_required').removeClass('display-hide')
                        }else{
                            $('#is_photo_service_required').addClass('display-hide');
                            // $('#isPhotoServiceRequired').prop('checked', false);
                        }
                        $("#myModal #office-ids").val(data.office_ids).trigger('change');
                        $("#myModal #taxMasterId").val(data.tax_master_id).trigger('change');
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Service: " + data.name)
                    } else {
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Service Edit - End*/

        /* Service Delete  - Start */
        $('#service-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('idsServices.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Service has been deleted successfully';
            deleteServiceRecord(url, table, message);
        });

/* Delete Record - Start */
         function deleteServiceRecord(url, table, message) {
            var url = url;
            var table = table;
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
                    type: 'GET',
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", message, "success");
                            if (table != null) {
                                table.ajax.reload();
                            }
                        } else if (data.success == false) {
                            if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                                swal("Warning", data.message, "warning");
                            } else {
                                swal("Warning", 'Data exists', "warning");
                            }
                        } else if (data.warning == true) {
                            swal("Warning",data.message, "warning");
                        } else {
                            console.log(data);
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
        }
/* Delete Record - End */
        /* Service Delete  - End */

    });

    function addnew(data=null) {

        $("#myModal").modal();
        $("#ids-service-form")[0].reset();
        $('#myModal input[name="id"]').val('');
        $('#myModal textarea[name="description"]').text('');
        $("#myModal #office-ids").val('').trigger('change');
        $("#myModal #taxMasterId").val('').trigger('change');
        $('#ids-service-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

     }

</script>
@stop
