{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Customer Shift')

@section('content_header')
<h1>Customer Shift</h1>
@stop

@section('content')
<style>
.option-adjust {
    display: inline !important;
    width: 350px !important;
}
</style>

<div id="message"></div>

<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$customerlist,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
<br>

<div class="add-new" data-title="Add New Customer Shift">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="customerShift-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Shift Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Customers Shift</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'customerShift-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="customer_id">
                    <label for="customer_id" class="col-sm-3 control-label" style="text-align: left;">Customer<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                     {{ Form::select('customer_id',[''=>'Select a customer']+$customerlist,null,array('class'=>'form-control select2 customer_select', 'style'=>"width: 100%;")) }}
                     <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="shiftname">
                    <label for="shiftname" class="col-sm-3 control-label" style="text-align: left;">Shift Name</label>
                    <div class="col-sm-9">
                      {{ Form::text('shiftname',null,array('class'=>'form-control','placeholder' => 'Shift Name','maxlength'=>100)) }}
                      <small class="help-block"></small>
                  </div>
                </div>
                <div id="starttime" class="form-group">
                    <label for="starttime" class="col-sm-3 control-label" style="text-align: left;">Start Time</label>
                    <div class="col-sm-9">
                        {{ Form::text('starttime',null,array('class'=>'form-control timepicker','placeholder' => 'Start Time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div id="endtime" class="form-group">
                    <label for="endtime" class="col-sm-3 control-label" style="text-align: left;">End Time</label>
                    <div class="col-sm-9">
                        {{ Form::text('endtime',null,array('class'=>'form-control timepicker','placeholder' => 'End Time')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
          <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal', 'onclick'=>"cancel()"))}}
          </div>
        {{ Form::close() }}
    </div>
</div>
</div>
@stop
@section('js')
<script>
    function cancel(){
        $('.customer_select').val('').trigger('change');
    }
    function collectFilterData() {
            return {
                client_id:$("#clientname-filter").val(),
            }
    }

    $(function () {
        $('.select2').select2();

        $('.timepicker').timepicki();

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#customerShift-table').DataTable({
                dom: 'lfrtBip',
                bProcessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    customize: function (xlsx) {
                      var sheet = xlsx.xl.worksheets['sheet1.xml'];
                      var col = $('col', sheet);
                      $(col[1]).attr('width', 40);
                  }

              },
              {
                extend: 'print',
                text: ' ',
                className: 'btn btn-primary fa fa-print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                text: ' ',
                className: 'btn btn-primary fa fa-envelope-o',
                action: function (e, dt, node, conf) {
                    emailContent(table, 'Customer Shift');
                }
            }
            ],
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax: {
                "url":'{{ route('customershift.list') }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());
                        },
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
            {data: 'DT_RowIndex', name: '',sortable:false},
            {
                data:'client_name',
                name:'client_name'
            },
            {data: 'shiftname', name: 'shiftname'},
            {data: 'starttime', name: 'starttime'},
            {data: 'endtime', name: 'endtime'},
            {data: null,
                orderable:false,
                render: function (a) {
                    var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + a.shift_id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + a.shift_id + '></a>';
                        @endcan
                        return actions;
                },
            }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        $(".client-filter").change(function(){
            table.ajax.reload();
        });

        /* Customer Shift Store - Start*/
        $('#customerShift-form').submit(function (e) {
            e.preventDefault();
            if($('#customerShift-form input[name="id"]').val()){
                var message = 'Customer shift has been updated successfully';
            }else{
                var message = 'Customer shift has been created successfully';
            }
            formSubmit($('#customerShift-form'), "{{ route('customershift.store') }}", table, e, message);
            cancel();
        });
        /* Customer Shift Store - End*/


        /*Edit Customer Shift - Start*/
        $("#customerShift-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("customershift.single",":id") }}';
            var url = url.replace(':id', id);
            $('#customerShift-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal select[name="customer_id"] option[value="'+data.customer_id+'"]').prop('selected',true);
                        $('#myModal input[name="shiftname"]').val(data.shiftname)
                        $('#myModal input[name="starttime"]').val(data.starttime)
                        $('#myModal input[name="endtime"]').val(data.endtime)
                        $(".select2").select2()
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Customer Shift: " + data.customer. client_name + ' ( ' +data.shiftname + ' ) ');

                    } else {
                        console.log(data);
                        swal("Oops", "Could not save data", "warning");
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
        /* Edit Customer Shift - End*/


        /* Customer Shift Delete  - Start */
        $('#customerShift-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url ="{{ route('customershift.destroy',':id') }}";
            var url = base_url.replace(':id',id);
            var message = 'Customer shift has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Customer Shift Delete  - End */
    });

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script src="{{ asset('js/timepicki.js') }}"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
 <link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
@stop
