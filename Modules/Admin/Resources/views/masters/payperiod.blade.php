
@extends('adminlte::page')

@section('title', 'Pay-Periods')

@section('content_header')
<h1>Pay Periods</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Pay Period">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="payperiod-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Year</th>
            <th>Pay Period Name</th>
            <th>Start Date</th>
            <th>Week 1 End Date</th>
            <th>Week 2 Start Date </th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Payperiod</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'payperiod-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <div class="form-group" id="year">
                    <label for="year" class="col-sm-3 control-label">Year <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <input type="hidden" name="id" value="0">
                        <select class="form-control has-error" name="year" id='year_id' style="width: 100%;"></select>

                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="pay_period_name">
                    <label for="pay_period_name" class="col-sm-3 control-label">Pay Period Name <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control"  name="pay_period_name" placeholder="Pay Period Name" value="">

                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="short_name">
                        <label for="short_name" class="col-sm-3 control-label">Short Name <span class="mandatory">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control"  name="short_name" placeholder="Short Name" value="">
                            <small class="help-block"></small>
                        </div>
                    </div>

                <div class="form-group" id="start_date">
                    <label for="start_date" class="col-sm-3 control-label">Start Date <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control datepicker" name="start_date" placeholder="Start Date" value="" max="2900-12-31">

                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="week_one_end_date">
                    <label for="start_date" class="col-sm-3 control-label">Week 1 End Date <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control datepicker" name="week_one_end_date" placeholder="Week 1 End Date" value="" max="2900-12-31">

                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="week_two_start_date">
                    <label for="start_date" class="col-sm-3 control-label">Week 2 Start Date <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control datepicker" name="week_two_start_date" placeholder="Week 2 Start Date" value="" max="2900-12-31">

                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="end_date">
                    <label for="inputDetail" class="col-sm-3 control-label">End Date <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control datepicker" name="end_date"  value="" placeholder="End Date" max="2900-12-31">

                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::reset('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div id="confirm" class="modal hide fade">
    <div class="modal-body">
        Delete?
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class=" btn btn-primary blue" id="delete">Delete</button>
        <button type="button" data-dismiss="modal" class="btn btn-primary blue">Cancel</button>
    </div>
</div>
@stop

@section('js')
<script>

    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            $('select#year_id').select2({
            dropdownParent: $("#myModal"),
            });
        var table = $('#payperiod-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
            buttons: [
                {
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
                        emailContent(table, 'Pay-Periods');
                    }
                }
            ],
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax: {
                "url":'{{ route('payperiod.list') }}',
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 6, "desc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'year', name: 'year'},
                {data: null, name: 'pay_period_name',render:function(data){
                    return (data.short_name!=null)?(data.pay_period_name+' <i>('+data.short_name+')</i>'):data.pay_period_name;
                }},
                {data: 'start_date', name: 'start_date'},
                {data: 'week_one_end_date', name: 'week_one_end_date'},
                {data: 'week_two_start_date', name: 'week_two_start_date'},
                {data: 'end_date', name: 'end_date'},


                {
                    data: null,
                    orderable:false,
                    render: function (o) {
                        var actions = '';
                        if(o.start_date > "{{date('Y-m-d')}}"){
                            @can('edit_masters')
                            actions = '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                            @endcan
                            @can('lookup-remove-entries')
                                actions+= '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                            @endcan
                        }
                        else{
                            @can('edit_masters')
                            actions = '<a title="Unable to edit, pay period expired" href="#" class="fa fa-pencil edit-disable"></a>';
                            @endcan
                            @can('lookup-remove-entries')
                                actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                            @endcan
                        }
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        $("#payperiod-table_wrapper").addClass("datatoolbar");

        /* Payperiod Save - Start*/
        $('#payperiod-form').submit(function (e) {
            e.preventDefault();
            var url = "";
            var payperiod_id = Number($('#myModal input[name="id"]').val());
            if (payperiod_id == 0) {
                url = "{{ route('payperiod.store') }}";
                message = 'Payperiod has been created successfully';
            } else {
                url = "{{ route('payperiod.payupdate') }}";
                message = 'Payperiod has been updated successfully';
            }
            formSubmit($('#payperiod-form'), url, table, e, message);
        });
        /* Payperiod Save - End*/

        /* Payperiod Edit - Start*/
        $("#payperiod-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            $('#payperiod-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: "{{route('payperiod.getPayPeriod')}}",
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {

                        $(".gj-calendar").css("z-index",2000);
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal select[name="year"]').val(data.year);
                        $('#myModal input[name="pay_period_name"]').val(data.pay_period_name);
                        $('#myModal input[name="short_name"]').val(data.short_name);
                        $('#myModal input[name="start_date"]').val(data.start_date);
                        $('#myModal input[name="week_one_end_date"]').val(data.week_one_end_date);
                        $('#myModal input[name="week_two_start_date"]').val(data.week_two_start_date);
                        $('#myModal input[name="end_date"]').val(data.end_date);
                        $('#myModal input[name="created_at"]').val(data.created_at);
                        $('#myModal input[name="updated_at"]').val(data.updated_at);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Pay Period: " + data.pay_period_name);
                    } else {
                        //alert(data);
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    //alert(xhr.status);
                    //alert(thrownError);
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Payperiod Edit - End*/

        /* Payperiod Delete Save - Start */
        $('#payperiod-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url ="{{ route('payperiod.destroy',':id') }}";
            var url = base_url.replace(':id',id);
            var message = 'Payperiod has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Payperiod Delete Save - End */


        var $select = $("#year select");
        var current_year = new Date().getFullYear();
        $select.append($('<option selected ></option>').val(current_year).html(current_year));
        for (i = 2017; i <= 2047; i++) {
            $select.append($('<option></option>').val(i).html(i));
        }

        /* Clear Search - Start */
        $("#clear-search").click(function () {
            $("#payperiod-table_filter input[type='search']").val('');
            table.search( '' ).draw();
        });
        /* Clear Search - End */
    });

</script>
@stop
