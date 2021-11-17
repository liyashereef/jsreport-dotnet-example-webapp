@extends('adminlte::page')

@section('title', 'Tax Master')

@section('content_header')
    <h1>Tax Master</h1>
@stop

@section('content')
    <div id="message"></div>
    <div class="add-new" data-title="Add New Tax Master">Add <span class="add-new-label">New</span></div>
    <table class="table table-bordered" id="taxmaster-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Short Name</th>
            <th>Tax-Percentage ( in %)</th>
            <th>Effective Date</th>
            <th>Action</th>

        </tr>
        </thead>
    </table>
    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Tax Master</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'taxmaster-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}
                <div class="modal-body">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>


                    <div class="form-group" id="name">
                        <label for="name" class="col-sm-3 control-label">Name <span class="mandatory">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="name" placeholder="Name" value="">
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="short_name">
                        <label for="short_name" class="col-sm-3 control-label">Short Name </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="short_name" placeholder="Short Name" value="">
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="tax_percentage">
                        <label for="tax_percentage" class="col-sm-3 control-label">Tax percentage (in %) <span
                                    class="mandatory" >*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="tax_percentage"
                                   placeholder="Tax percentage (in %) Eg: 100.00" value="" maxlength="6">
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="effective_from_date">
                        <label for="effective_from_date" class="col-sm-3 control-label">Effective Date <span
                                    class="mandatory">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control datepicker" name="effective_from_date"
                                   placeholder="Effective Date" value="" max="2900-12-31">
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

                var table = $('#taxmaster-table').DataTable({

                    bProcessing: false,
                    responsive: true,
                    dom: 'lfrtBip',

                    buttons: [

                    ],
                    processing: true,
                    serverSide: true,
                    fixedHeader: true,
                    ajax: {
                        "url": '{{ route('tax-master.list') }}',
                        "error": function (xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    order: [[1, "asc"]],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    columns: [
                        {data: 'DT_RowIndex', name: '', sortable: false},
                        {data: 'name', name: 'name'},
                        {data: 'short_name', name: 'short_name'},
                        {  data: null, sortable:false, name: 'tax_percentage', render: function (data) {
                           return data.tax_master_log != null ? (data.tax_master_log.tax_percentage) : 'Archived';
                            }	
                        },	
                        {	
                            data: null, sortable:false, name: 'effective_from_date', render: function (data) {	
                                return data.tax_master_log != null ? (data.tax_master_log.effective_from_date) : 'Archived';	
                            }	
                        },	

                        {
                            data: null,
                            orderable: false,
                            render: function (data, type, row) {
                                
                                var actions = '';
                                actions = '<a href="{{route("tax-master.single", ["id" => ""])}}/' + row.id + '" class="view fa fa-eye pr-10" data-id=' + row.id + '></a>';
                                if(((row.tax_master_log) == null)){
                                  actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + row.id + '></a>';
                                }
                                 else if (((row.tax_master_log.effective_from_date) >= "{{(\Illuminate\Support\Carbon::today())->toDateString()}}")	
                                 ||((row.tax_master_log.effective_from_date) < "{{(\Illuminate\Support\Carbon::today())->toDateString()}}")) {
                                 actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" TITLE="Archive" data-id=' + data.tax_master_log.id + '></a>';	
                                }
                                /*else{

                                    actions += '<a href="#" class="edit fa fa-pencil" data-id=' + row.id + '></a>';
                                }*/
                                return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
                // console.log(e.stack);
            }

            $("#tax-master_wrapper").addClass("datatoolbar");

            /* Taxmaster Save - Start*/
            $('#taxmaster-form').submit(function (e) {
                e.preventDefault();
                var url = "";
                var taxmaster_id = Number($('#myModal input[name="id"]').val());
                if (taxmaster_id == 0) {
                    url = "{{ route('tax-master.store') }}";
                    message = 'Tax master has been created successfully';
                } else {
                    url = "{{ route('tax-master.expenseTrackerupdate') }}";
                    message = 'Tax master has been updated successfully';
                }
                formSubmit($('#taxmaster-form'), url, table, e, message);
            });
            /* Taxmaster Save - End*/

            /* Taxmaster Edit - Start*/
            $("#taxmaster-table").on("click", ".edit", function (e) {
                id = $(this).data('id');
                $('#taxmaster-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $.ajax({
                    url: "{{route('tax-master.getExpenseTracker')}}",
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                        if (data) {

                            $(".gj-calendar").css("z-index", 2000);
                            $('#myModal input[name="id"]').val(data.id);

                            $('#myModal input[name="name"]').val(data.name);
                            $('#myModal input[name="short_name"]').val(data.short_name);
                            /*$('#myModal input[name="name"]').prop('readonly',true);
                            $('#myModal input[name="short_name"]').prop('readonly',true);*/
                            $('#myModal input[name="tax_percentage"]').val(data.tax_percentage);
                            $('#myModal input[name="effective_from_date"]').val(data.effective_from_date);
                            $('#myModal input[name="created_at"]').val(data.created_at);
                            $('#myModal input[name="updated_at"]').val(data.updated_at);
                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Edit Tax Master");
                        } else {
                            //alert(data);
                           // console.log(data);
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
            /* Taxmaster Edit - End*/

            /* Taxmaster Delete Save - Start */
            $('#taxmaster-table').on('click', '.delete', function (e) {

                var id = $(this).data('id');
                var base_url = "{{ route('tax-master.destroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'Tax master has been archived successfully';
                archiveRecord(url, table, message);
            });
            /* Taxmaster Delete Save - End */
        });

        /* Archive Record - Start */
        function archiveRecord(url, table, message) {
            var url = url;
            var table = table;
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, archive",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal("Archived", message, "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else if (data.success == false) {
                                swal("Warning", 'Data exists', "warning");
                            } else if (data.warning) {
                                swal("Warning", 'Competency exists for the category', "warning");
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

        /* Archive Record - End */
    </script>
    <style>
        .pr-10{
            padding-right: 10px;
        }
    </style>
@stop
