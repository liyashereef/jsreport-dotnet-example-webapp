@extends('adminlte::page')
@section('title', 'Holidays')
@section('content_header')
<h1>Holidays</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Holiday">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="holiday-table">
    <thead>
        <tr>
            <th>#</th>
    
            <th>Holiday</th>
            <th>Payment status</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
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
                <h4 class="modal-title" id="myModalLabel">Holiday Payment Allocation</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'holiday-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="year">
                    <label for="year" class="col-sm-3 control-label">Year</label>
                    <div class="col-sm-9">
                        <select class="form-control has-error" name="year"></select>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="holiday" class="form-group">
                    <label for="holiday" class="col-sm-3 control-label">Holiday</label>
                    <div class="col-sm-9">
                        {{ Form::text('holiday',null,array('class'=>'form-control datepicker','placeholder' => 'Holiday','max'=>'2900-12-31')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::text('description',null,array('class'=>'form-control','placeholder' => 'Holiday Description')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
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
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#holiday-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
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
                            emailContent(table, 'Holidays');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('holidaypaymentallocation.list') }}",
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
                        data: 'holidayid',
                        name: 'holidayid'
                    },
                    {
                        data: 'paymentstatus',
                        name: 'paymentstatus'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (o) {
                            if (o.holiday >= "{{date('Y-m-d')}}") {
                                @can('edit_masters')
                                actions = '<a href="#" class="edit fa fa-pencil" data-id=' + o.id +'></a>';
                                @endcan
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' +o.id + '></a>';
                                @endcan
                            } else {
                                actions = '<a href="#" class="fa fa-pencil edit-disable"></a>';
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" data-id=' +o.id + '></a>';
                                @endcan
                            }
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        /* Holiday Store - Start*/
        $('#holiday-form').submit(function (e) {
            e.preventDefault();
            if($('#holiday-form input[name="id"]').val()){
                var message = 'Holiday has been updated successfully';
            }else{
                var message = 'Holiday has been created successfully';
            }
            formSubmit($('#holiday-form'), "{{ route('holiday.store') }}", table, e, message);
        });
        /* Holiday Store - End*/

        /* Holiday Edit - Start*/
        $("#holiday-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("holiday.single",":id") }}';
            var url = url.replace(':id', id);
            $('#holiday-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal select[name="year"]').val(data.year)
                        $('#myModal input[name="holiday"]').val(data.holiday)
                        $('#myModal input[name="description"]').val(data.description)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Holiday: " + data.description)
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
        /* Holiday Edit - End*/

        /* Holiday Delete  - Start */
        $('#holiday-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('holiday.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Holiday has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Holiday Delete  - End */

    });
    var $select = $('#myModal select[name="year"]');
    $select.append($('<option selected disabled></option>').val('Select').html('Select'));
    for (i = 2017; i <= 2047; i++) {
        $select.append($('<option></option>').val(i).html(i));
    }
</script>
@stop
