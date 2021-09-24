@extends('adminlte::page')
@section('title', 'KPI Groups')
@section('content_header')
<h1>KPI Groups</h1>
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }

    .select2 .select2-container {
        width: 12% !important;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .form-group {
        margin: auto !important;
    }
</style>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New KPI Group">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="kpi-group-table">
    <thead>
        <tr>
            <th></th>
            <th>Parent Group</th>
            <th>Name</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="kpi-group-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="kpi-group-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="kpi-group-modalLabel">KPI Group</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'kpi-group-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="row mt-10">
                    <div class="col-md-6">
                        <label>Group Name</label>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="name">
                            <input type="text" name="name" placeholder="Enter group name" class="form-control" />
                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>

                <div class="row mt-10">
                    <div class="col-md-6">
                        <label>Active</label>
                    </div>
                    <div class="col-md-6">
                        <input type="checkbox" id="kpi-group-is-active" name="is_active" value="1" checked />
                    </div>
                </div>

                <div class="row mt-10">
                    <div class="col-md-6">
                        <label>Parent Group</label>
                    </div>
                    <div class="col-md-6">
                        <select name="parent_id" id="kpi-parent-group" class="form-control">
                            <option value="">None</option>
                        </select>
                    </div>
                </div>
            </div>


            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>
@stop

@section('js')
<script>
    let table;

    const kpiGroup = {
        loadTable() {
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                table = $('#kpi-group-table').DataTable({
                    bprocessing: false,
                    processing: true,
                    serverSide: true,
                    fixedHeader: true,
                    dom: 'lfrtBip',
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
                            action: function(e, dt, node, conf) {
                                emailContent(table, 'Services');
                            }
                        }
                    ],
                    ajax: {
                        "url": "{{ route('admin.kpi.groups.list') }}",
                        "error": function(xhr, textStatus, thrownError) {
                            if (xhr.status === 401) {
                                window.location = "{{ route('login') }}";
                            }
                        },
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: '',
                            sortable: false
                        },
                        {
                            data: null,
                            sortable: false,
                            render: function(o) {
                                if (o.parent == null) {
                                    return '--';
                                }
                                return `<p>${o.parent.name}</p>`
                            },
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'active_fld',
                            name: 'active_fld'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function(o) {
                                var actions = "";
                                @can('edit_masters')
                                actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                                @endcan
                                @can('lookup-remove-entries')
                                actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                                @endcan
                                return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }
        },
        resetForm() {
            $("#kpi-group-form")[0].reset();
            $('#kpi-group-modal input[name="id"]').val('');
            $("#kpi-group-is-active").prop("checked", true);
            $('#kpi-group-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $("#kpi-group-modal").modal();

        },
        populateParentGroup(editData, groups) {
            let _isEditMode = editData == null ? false : true;
            let options = '<option value="">None</option>';

            groups.forEach(function(item, index) {
                let _selected = "";

                //filter same group as parent
                if (_isEditMode && editData.id == item.id) {
                    return;
                }

                //Edit mode set checked
                if (_isEditMode && item.id == editData.parent_id) {
                    _selected = 'selected';
                }
                //Generate options
                options += `<option value="${item.id}" ${_selected}>${item.name}</option>`;
            });

            $('#kpi-parent-group').empty().append(options);
        },
        onSaveForm() {
            let root = this;
            let _form = $('#kpi-group-form');
            let message = 'KPI group has been created successfully';
            if ($('#kpi-group-form input[name="id"]').val()) {
                message = 'KPI group has been updated successfully';
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('admin.kpi.groups.store')}}",
                type: 'POST',
                data: root.collectModalData(),
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: message,
                            type: "success",
                            icon: "success",
                            button: "Ok",

                        }, function() {
                            $('#kpi-group-modal').modal('hide');
                            table.ajax.reload();
                        });
                    } else {
                        swal("Alert", "Something went wrong", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, _form);
                },
            });
        },
        collectModalData() {
            let formData = new FormData($('#kpi-group-form')[0]);
            let isActive = $(`#kpi-group-form input[name="is_active"]`).is(":checked");
            // alert(isActive);
            // if (isActive) {
            //     formData.set('is_active', 1);
            // }else{
            //     formData.set('is_active', 0);
            // }
            return formData;
        },
        loadParentGroups(rootData) {
            let root = this;
            $.get({
                url: '{{ route("admin.kpi.groups.parents") }}',
                type: "GET",
                global: false,
                success: function(res) {
                    root.populateParentGroup(rootData, res);
                },
            });
        },
        onFormEdit(el) {
            let root = this;
            id = $(el).data('id');
            var url = '{{ route("admin.kpi.groups.single",":id") }}';
            var url = url.replace(':id', id);
            $('#kpi-group-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        $('#kpi-group-modal input[name="id"]').val(data.id);
                        $('#kpi-group-modal input[name="name"]').val(data.name);
                        root.loadParentGroups(data);
                        $("#kpi-group-is-active").prop("checked", data.is_active == 1 ? true : false);
                        $("#kpi-group-modal").modal('show');
                        $('#kpi-group-modal .modal-title').text("Edit KPI group: " + data.name)
                    } else {
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        },
        onDelete(el) {
            var id = $(el).data('id');
            var base_url = "{{ route('admin.kpi.groups.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'KPI group has been deleted successfully';
            this.deleteServiceRecord(url, table, message);
        },
        deleteServiceRecord(url, table, message) {
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
                function() {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(data) {
                            if (data.success) {
                                swal("Deleted", 'KPI group has been deleted successfully', "success");
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
                                swal("Warning", data.message, "warning");
                            } 
                        },
                        error: function(xhr, textStatus, thrownError) {
                        },
                        contentType: false,
                        processData: false,
                    });
                });

        },
        init() {
            let root = this;
            this.loadTable();

            $('.add-new').on('click', function() {
                root.resetForm();
                root.loadParentGroups(null);
            });

            //on submit
            $('#kpi-group-form').submit(function(e) {
                e.preventDefault();
                root.onSaveForm();
            });

            //on edit
            $("#kpi-group-table").on("click", ".edit", function(e) {
                root.onFormEdit(this);
            });

            //on delete
            $('#kpi-group-table').on('click', '.delete', function(e) {
                root.onDelete(this);
            });
        }
    }

    $(function() {
        kpiGroup.init();
    });
</script>
@stop