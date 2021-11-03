@extends('adminlte::page')
@section('title', 'RFP Response Type')
@section('content_header')
    <h1>RFP Response Type</h1>
@stop @section('content')
    <div class="add-new" data-title="Add New RFP Response Type">Add
        <span class="add-new-label">New</span>
    </div>
    <table class="table table-bordered" id="response-type-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Response Type</th>
            <th>Actions</th>
        </tr>
        </thead>
    </table>

    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <span id="field_error"></span>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">RFP Response Type</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'rfp-response-type-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                <input type="hidden" name="id" id="id"/>
                <div class="modal-body">
                    <div id="form-errors"></div>
                    <div class="form-group" id="rfpResponseType">
                        <label for="rfpResponseType" class="col-sm-3 control-label">RFP Response Type<span
                                    class="mandatory">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control has-error" name="rfpResponseType"
                                   id="rfpResponseType" placeholder="RFP Response Type" value=""/>
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
@stop

@section('js')
    <script type="text/javascript">
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#response-type-table').DataTable({
                bProcessing: false,
                dom: 'lfrtBip',
                columnDefs: [
                    {width: 200, targets: 0}
                ],
                fixedColumns: true,
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function (e, dt, node, conf) {
                            emailContent(table, 'RFP Response Type');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                ajax: "{{ route('rfp-response-type.list') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [
                    [2, "asc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable: false,
                    width: "10%",
                },
                    {
                        data: 'rfp_response_type',
                        name: 'rfp_response_type',
                        width: "50%",
                    },
                    {
                        data: null,
                        sortable: false,
                        width: "25%",
                        render: function (o) {
                            var actions = '';
                            actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" ' +
                                'data-id=' + o.id + ' data-change-title=\'' + o.rfp_response_type + '\' ></a>';
                            @can('lookup-remove-entries')
                                actions += '<a href="#" class="delete deletereason {{Config::get('globals.deleteFontIcon')}}" ' +
                                'data-id=' + o.id + ' data-change-title=\'' + o.rfp_response_type + '\' ></a>';
                            @endcan
                                return actions;
                        },
                    }
                ]
            });
        } catch (e) {

        }

        $(document).on("submit", "#rfp-response-type-form", function (e) {
            e.preventDefault();
            var rfpResponseType = $("#rfpResponseType").val();
            var id = $("#id").val();
            var url = "{{route('rfp-response-type.store')}}";
            var message = '';
            $form = $('#rfp-response-type-form');
            if (id < 1) {
                message = 'RFP response type has been created successfully';
            } else {
                message = 'RFP response type has been updated successfully';
            }
            formSubmit($('#rfp-response-type-form'), url, table, e, message);
        });

        /* RFP response - Start */
        $("#response-type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("rfp-response-type.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="rfpResponseType"]').val(data.rfp_response_type)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit RFP Response: " + data.rfp_response_type)
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
        /* RFP response Edit- End */
        /* RFP response Delete - Start */
        $('#response-type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('rfp-response-type.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'RFP response has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* RFP response Delete- End */
    </script>
@endsection
