@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
<style>
    #table-id .fa {
        margin-left: 11px;
    }
</style>
@stop
@section('content')
<div class="table_title">
    <h4>Visitor Management</h4>
    <!-- Trigger the modal with a button -->
<button type="button" class="btn btn-lg add-new" onclick="addnew()" style="margin-bottom: 15px;">Add Visitor</button>
</div>
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$project_list,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
<br>
<div id="message"></div>

<!---Start---- Create new visitor ---- Modal -->

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Create New Visitor</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      {{ Form::open(array('url'=>'#','id'=>'visitor-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
      {{ Form::hidden('id', null) }}

      <div class="modal-body">

             <div class="form-group" id="customerId">
                <label for="customerId" class="col-sm-3 control-label">Customer </label>
                <div class="col-sm-11">
                    {!!Form::select('customerId', [null=>'Please Select'] + $project_list,null, ['class' => 'form-control','id'=>'project-filter'])!!}
                    <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="firstName">
                <label for="first_name" class="col-sm-3 control-label">First Name </label>
                <div class="col-sm-11">
                    {{ Form::text('firstName','',['class' => 'form-control','id'=>'firstName']) }}
                    <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="lastName">
                <label for="first_name" class="col-sm-3 control-label">Last Name </label>
                <div class="col-sm-11">
                    {{ Form::text('lastName','',['class' => 'form-control','id'=>'lastName']) }}
                    <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="email">
                <label for="email" class="col-sm-3 control-label">Email </label>
                <div class="col-sm-11">
                    {{ Form::text('email','',['class' => 'form-control','id'=>'email']) }}
                    <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="phone">
                <label for="phone" class="col-sm-3 control-label">Phone </label>
                <div class="col-sm-11">
                {{ Form::text('phone',null,array('class'=>'form-control phone','placeholder' => 'Phone Number [ format (XXX)XXX-XXXX ]')) }}
                 <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="visitorTypeId">
                <label for="visitorTypeId" class="col-sm-3 control-label">Visitor Type </label>
                <div class="col-sm-11">
                    {!!Form::select('visitorTypeId', [null=>'Please Select'] + $visitorTypeList,null, ['class' => 'form-control','id'=>'visitorTypeId'])!!}
                    <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="visitorStatusId">
                <label for="visitorStatusId" class="col-sm-3 control-label">Visitor Status </label>
                <div class="col-sm-11">
                    {!!Form::select('visitorStatusId', [null=>'Please Select'] + $visitorStatusList,null, ['class' => 'form-control','id'=>'visitorStatusId'])!!}
                    <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="barCode">
                <label for="barCode" class="col-sm-3 control-label">QR Code </label>
                <div class="col-sm-11">
                    {{ Form::text('barCode','',['class' => 'form-control','id'=>'barCodeValue']) }}
                    <small class="help-block"></small>
                </div>
             </div>

             <div class="form-group" id="note">
                <label for="note" class="col-sm-3 control-label">Note </label>
                <div class="col-sm-11">
                    {!!Form::textarea('notes',null, ['class' => 'form-control','id'=>'note','rows'=>'3'])!!}
                    <small class="help-block"></small>
                </div>
             </div>

      </div>

      <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
        {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
      </div>

    </div>

    {{ Form::close() }}

  </div>
</div>

<!---End---- Create new visitor ---- Modal -->

<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
            <th>#</th>
             <th>Client</th>
             <th>Visitor Type</th>
             <th>Visitor Status</th>
             <th>First Name</th>
             <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>QR Added</th>
            <th>Notes</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>


@stop
@section('scripts')
<script>
    $(function () {
        $('#project-filter').select2();//Added Select2 to project listing
        $("#clientname-filter").select2()
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'lfrtip',
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url":'{{ route('client-visitor.list') }}',
                    "data": function ( d ) {
                        d.payperiod = $("#payperiod-filter").val();
                        d.client_id=$("#clientname-filter").val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                order: [[0, 'desc']],
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                {
                    data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false
                },
                {
                    data: null,
                    sortable: false,
                    name: 'customer',
                    render: function (o) {
                        var customer = '';
                        if(o.customerId){
                        customer = o.customer.project_number+' - '+o.customer.client_name;
                        }
                        return customer;
                    },
                },
                {
                    data: null,
                    sortable: false,
                    name: 'visitorType',
                    render: function (o) {
                        var visitorType = '';
                        if(o.visitorTypeId){
                            visitorType = o.visitorType.type;
                        }
                        return visitorType;
                    },
                },
                {
                    data: null,
                    sortable: false,
                    name: 'visitorStatus',
                    render: function (o) {
                        var visitorStatus = '';
                        if(o.visitorStatusId){
                            visitorStatus = o.visitorStatus.name;
                        }
                        return visitorStatus;
                    },
                },
                {data: 'firstName', name: 'firstName'},
                {data: 'lastName', name: 'lastName'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {
                    data: null,
                    name: 'barCode',
                    render: function (o) {
                      if(o.barCode == null || o.barCode == ''){
                          return 'No';
                      }
                      return 'Yes';
                    },
                },
                {data: 'notes', name: 'notes'},
                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions = '<a href="#" class="edit fa fa-edit" data-id=' + o.id + ' ></a>';
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }

                ],
            });

        } catch(e){
            console.log(e.stack);
        }
    });

    $(".client-filter").change(function(){
            table.ajax.reload();
        });
/* Posting data to Visitor - Start*/
$('#visitor-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('client-visitor.store') }}";
            var formData = new FormData($('#visitor-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", "The record has been saved", "success");
                        table.ajax.reload();
                        $("#visitor-form")[0].reset();
                        $("#project-filter").val('').trigger('change');;
                        // $('#mdl_save_change').closest('form').find("input[type=text], textarea,select").val("");
                        $('#myModal').modal('hide');
                    } else {
                        swal("Error", "The record not saved", "error");
                    }
                },
                fail: function (response) {

                },
                error: function (xhr, textStatus, thrownError) { console.log(xhr);
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Posting data to AdminController - End*/


    $('#table-id').on('click', '.edit', function(e){
        var id = $(this).data('id');
        var base_url = "{{route('client-visitor.single', ':id')}}";
        var url = base_url.replace(':id', id);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type: 'GET',
            success: function (data) {
               if(data){
                addnew(data);
               }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            contentType: false,
            processData: false,
        });
    });

    /***** Visitor  Delete - Start */
    $('#table-id').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('client-visitor.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action Proceed?",
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

        function addnew(data=null) {

        $("#myModal").modal();

        $("#visitor-form")[0].reset();
        $("#project-filter").val('').trigger('change');
        $('input[name="id"]').val('');
        $('textarea[name="notes"]').text('');
        $('#visitor-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

        if(data != null){

            $('select[name="customerId"]').val(data.customerId);
            $("#project-filter").val(data.customerId).trigger('change');
            $('select[name="visitorStatusId"]').val(data.visitorStatusId);
            $('select[name="visitorTypeId"]').val(data.visitorTypeId);

            $('textarea[name="notes"]').text(data.notes);
            $('input[name="id"]').val(data.id);
            $('input[name="firstName"]').val(data.firstName);
            $('input[name="lastName"]').val(data.lastName);
            $('input[name="email"]').val(data.email);
            $('input[name="phone"]').val(data.phone);
            $('input[name="barCode"]').val(data.barCode);
        }else{

        }

    }
/***** Visitor Delete- End */

</script>
<style type="text/css">

</style>

@stop
