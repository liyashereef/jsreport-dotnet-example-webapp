@extends('adminlte::page')
@section('title', 'Custom Question')
@section('content_header')
    <h1>Client Onboarding Template</h1>
@stop
@section('content')
    <div id="message"></div>
    <div class="add-new" data-title="Add New Template">Add
        <span class="add-new-label">New</span>
    </div>
    <table class="table table-bordered" id="template-table">
        <thead>
        <tr>
            <th>Sort Order</th>
            <th>Section</th>
            <th>Steps</th>
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
                    <h4 class="modal-title" id="myModalLabel">Section</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'onboarding-template-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
                <div class="modal-body">
                    <div class="form-group row" id="test_question">
                        <label for="question" class="col-sm-12">Section</label>
                        <div class="col-sm-10">
                            {{ Form::text('section',null,array('class' => 'form-control', 'placeholder'=>'Section', 'required'=>'required')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group row" id="sort_order">
                        <label for="display_order" class="col-sm-12">Sort Order</label>
                        <div class="col-sm-10">
                            {{ Form::number('sort_order',null,array('class' => 'form-control', 'placeholder'=>'Order')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div id="dynamic-rows">
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
@include('admin::contracts.partials.moreSteps')
@stop
@section('js')
    <script src="{{ asset('js/moreel.js') }}"></script>
    <script>
        $(function () {
            let divParam = {
                containerDiv: '#dynamic-rows',
                addButton: '.add_button',
                form: '#onboarding-template-form',
            };
            let moreSteps = new MoreEl('step', divParam);
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                var table = $('#template-table').DataTable({
                    dom: 'lfrtip',
                    bprocessing: false,
                    buttons: [{
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
                                columns: [0, 1, 2]
                            }
                        },
                        {
                            extend: 'print',
                            text: ' ',
                            className: 'btn btn-primary fa fa-print',
                            exportOptions: {
                                columns: [0, 1, 2]
                            }
                        },
                        {
                            text: ' ',
                            className: 'btn btn-primary fa fa-envelope-o',
                            action: function (e, dt, node, conf) {
                                emailContent(table, 'Templates');
                            }
                        }
                    ],
                    processing: true,
                    serverSide: true,
                    fixedHeader: true,
                    ajax: {
                        "url": "{{ route('client-onboarding-template.list') }}",
                        "error": function (xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    order: [[0, "asc"]],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    columns: [{
                        data: 'sort_order',
                        name: 'sort_order',
                    },
                        {
                            data: 'section',
                            name: 'section'
                        },
                        {
                            data: 'steps',
                            name: 'step.step',

                        },
                        {
                            data: 'actions',
                            sortable: false,
                            searchable: false
                        }
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }
            /* Template Store - Start*/

            $('#onboarding-template-form').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                url = "{{ route('client-onboarding-template.store') }}";
                var formData = new FormData($('#onboarding-template-form')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success) {
                            swal("Saved", "Template saved successfully", "success");
                            $("#myModal").modal('hide');
                            table.ajax.reload();
                        } else {
                            console.log(data);
                        }
                    },
                    fail: function (response) {
                        console.log('here');
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form, true);
                    },
                    contentType: false,
                    processData: false,
                });
            });
            /* Template Store - End*/

            /* Template Edit - Start*/
            $("#template-table").on("click", ".edit", function (e) {
                $('#onboarding-template-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                var id = $(this).data('id');
                var url = '{{ route("client-onboarding-template.single",":id") }}';
                var url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            moreSteps.initElDiv(true);
                            let stepIdElSelector = 'input[name="step-id[]"]';
                            let stepElSelector = 'input[name="step[]"]';
                            let sortElSelector = 'input[name="sort[]"]';
                            $('#myModal input[name="id"]').val(data.id);
                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Edit Template");
                            $('#myModal input[name="id"]').val(data.id);
                            $('#myModal input[name="section"]').val(data.section);
                            $('#myModal input[name="sort_order"]').val(data.sort_order);
                            for(let i = 0; i < data.step.length; i++) {
                                let stepId = data.step[i].id;
                                let stepName = data.step[i].step;
                                let stepSortOrder = data.step[i].sort_order;
                                let newSteps = moreSteps.addRow();
                                $(newSteps).find(stepIdElSelector).val(stepId);
                                $(newSteps).find(stepElSelector).val(stepName);
                                $(newSteps).find(sortElSelector).val(stepSortOrder);
                            }
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


            /* Template Delete  - Start */
            $('#template-table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                var base_url = "{{ route('client-onboarding-template.destroy',':id') }}";
                var url = base_url.replace(':id', id);
                var message = 'Template deleted successfully';
                deleteRecord(url, table, message);
            });
            /* Template Delete  - End */

            $('.add-new').click(function () {
                $("#myModal").modal();
                var title = $(this).data('title');
                $("#myModal").modal();
                $('#myModal form').trigger('reset');
                $('#myModal').find('input[name=id]').val('');
                $('#myModal').find('textarea').val('');
                $('#myModal .modal-title').text(title);
                $('#myModal input[name="is_active"]').prop('checked', true);
                $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                moreSteps.initElDiv();
            });
        });
    </script>
@stop
