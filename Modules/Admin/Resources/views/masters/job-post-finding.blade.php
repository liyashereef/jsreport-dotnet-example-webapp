@extends('adminlte::page')
@section('title', 'Job Post Finding')
@section('content_header')

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <h1>Job Post Finding</h1>
@stop
@section('content')
    <div class="add-new" data-title="Add New Job Post Finding">Add
        <span class="add-new-label">New</span>
    </div>
    <table class="table table-bordered" id="job-post-finding-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Job Post Finding</th>
            <th>Order Sequence</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
        </thead>
    </table>
    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'job-post-finding-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}
                <div class="modal-body">
                    <div class="form-group " id="job_post_form">
                        <div class="row form-group">
                            <label for="job_post_finding" class="col-sm-3 control-label">Job Post Finding
                                <span class="mandatory">*</span>
                            </label>
                            <div class="col-sm-5">
                                {{ Form::text('job_post_finding',null,array('class'=>'form-control')) }}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="order_sequence" class="col-sm-3 control-label">Order
                                <span class="mandatory">*</span>
                            </label>
                            <div class="col-sm-2">
                                {{ Form::number('order_sequence',null,array('class'=>'form-control')) }}
                                <small class="help-block"></small>
                            </div>
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
@stop @section('js')
    <script>
        $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                var table = $('#job-post-finding-table').DataTable({
                    bProcessing: false,
                    responsive: true,
                    dom: 'lfrtBip',
                    buttons: [
                        {
                            extend: 'pdfHtml5',
                            text: ' ',
                            className: 'btn btn-primary fa fa-file-pdf-o',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: ' ',
                            className: 'btn btn-primary fa fa-file-excel-o',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'print',
                            text: ' ',
                            className: 'btn btn-primary fa fa-print',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            text: ' ',
                            className: 'btn btn-primary fa fa-envelope-o',
                            action: function (e, dt, node, conf) {
                                emailContent(table, 'Security Clearance');
                            }
                        }
                    ],
                    processing: false,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('job-post-finding.list') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    order: [
                        [0, "asc"]
                    ],
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
                            data: 'job_post_finding',
                            name: 'job_post_finding'
                        },
                        {
                            data: 'order_sequence',
                            name: 'order_sequence'
                        },
                        {data: 'created_at', name: 'created_at'},
                        {data: 'updated_at', name: 'updated_at'},
                        {
                            data: null,
                            sortable: false,
                            render: function (o) {
                                var actions = '';
                                @can('edit_masters')
                                    if(o.is_editable !== 0) {
                                        actions = '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
                                    }
                                @endcan
                                        @can('lookup-remove-entries')
                                if(o.is_editable !== 0) {
                                    actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                                }
                                @endcan
                                    return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }

            /* Job Post Finding save - Start*/
            $('#job-post-finding-form').submit(function (e) {
                e.preventDefault();
                if ($('#job-post-finding-form input[name="id"]').val()) {
                    var message = 'Updated successfully';
                } else {
                    var message = 'Created successfully';
                }
                formSubmit($('#job-post-finding-form'), "{{ route('job-post-finding.store') }}", table, e, message);
            });
            /* Job Post Finding save - End*/


            /* Job Post Finding delete - Start */
            $('#job-post-finding-table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                var base_url = "{{ route('job-post-finding.destroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'Deleted successfully';
                deleteRecord(url, table, message);
            });
            /* Job Post Finding delete - End */

            /* Job Post Finding Edit - Start */
            $("#job-post-finding-table").on("click", ".edit", function (e) {
                var id = $(this).data('id');
                var url = '{{ route("job-post-finding.single",":id") }}';
                var url = url.replace(':id', id);
                $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                    '');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            $('#myModal input[name="id"]').val(data.id)
                            $('#myModal input[name="job_post_finding"]').val(data.job_post_finding)
                            $('#myModal input[name="order_sequence"]').val(data.order_sequence)
                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Edit: " + data.job_post_finding)
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
            /* Job Post Finding Edit - End */

        });
    </script>
@stop
