@extends('adminlte::page')
@section('title', 'KPI Headers')
@section('content_header')
<h1>KPI Headers</h1>
@stop

@section('css')
<style>
    .js-new-kpi-header-btn {
        float: right;
    }
</style>
@stop

@section('content')
<div class="row" style="margin-top: 10px; margin-bottom: 10px;">
    <div class="col-md-10"></div>
    <div class="col-md-2">
        <div class="add-new js-new-kpi-header-btn">Add Header</div>
    </div>
</div>

<div class="">
    <table id="table-kpi-headers" class="table table-bordered" style="text-align: center; width:100%;">
        <thead>
            <tr>
                <th class="sorting_disabled">#</th>
                <th class="sorting_disabled">Header Name</th>
                <th class="sorting_disabled">Status</th>
                <th class="sorting_disabled">Created At</th>
                <th class="sorting_disabled"></th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" role="dialog" id="kpiHeaderModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-10">
                        <h4 class="modal-title">Header</h4>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="supervisorclose" data-dismiss="modal" aria-label="Close" style="float: right">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
            </div>
            <form id="kpi-header-form">
                <div class="modal-body">
                    <input type="hidden" name="id" value="" />
                    <div class="row">
                        <div class="col-md-6">
                            <label>Header name</label>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="name">
                                <input type="text" placeholder="Enter header name" class="form-control" name="name" required />
                                <small class="help-block"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Active</label>
                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" name="is_active" value="1" checked />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary blue js-kpih-cancel">Cancel</button>
                    <button type="button" class="btn btn-primary blue js-kpih-save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    const kpiHeaderScript = {
        data: {
            kpiHeaderTable: null,
        },
        resetHeaderModal() {
            let modal = '#kpiHeaderModal'
            $(modal + ' input[name="id"]').val('');
            $(modal + ' input[name="name"]').val('');
            $(modal + ' input[name="is_active"]').val('1');
            $(modal + ' .modal-title').text("Add Header");
            $('#kpi-header-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        },
        collectHeaderData() {
            let modal = '#kpiHeaderModal'
            let isActive = $(modal + ' input[name="is_active"]').is(":checked");
            if (isActive) {
                is_active = 1;
            } else {
                is_active = 0;
            }
            return {
                'id': $(modal + ' input[name="id"]').val(),
                'name': $(modal + ' input[name="name"]').val(),
                'is_active': is_active,
            }
        },
        loadKpiHeaderTable() {
            $.fn.dataTable.ext.errMode = 'throw';
            let listUrl = '{{ route("admin.kpi-header.list") }}';

            this.data.kpiHeaderTable = $('#table-kpi-headers').DataTable({
                bProcessing: false,
                responsive: true,
                ajax: listUrl,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
                        data: null,
                        sortable: false,
                        render: function(o) {
                            let activeLabel = o.is_active ? 'Active' : 'Inactive';
                            return `<p>${activeLabel}</p>`
                        },
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            let actions = '';
                            @can('edit_masters')
                            actions += '<a href="javascript:void(0)" class="edit js-kpih-edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<a href="javascript:void(0)" class="delete js-kpih-delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        },
        onDeleteHeader(el) {
            let root = this;
            let id = $(el).data('id');
            let base_url = "{{ route('admin.kpi-header.destroy',':id') }}";
            let url = base_url.replace(':id', id);

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
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if (data.success) {
                                swal("Deleted", data.message, "success");
                                root.data.kpiHeaderTable.ajax.reload();
                            } else {
                                swal("Warning", data.message, "warning");
                            }
                        },
                        error: function(xhr, textStatus, thrownError) {},
                        contentType: false,
                        processData: false,
                    });
                });

        },
        onEditHeader(el) {
            let id = $(el).data('id');
            let baseUrl = '{{ route("admin.kpi-header.single",":id") }}';
            let url = baseUrl.replace(':id', id);
            $('#kpi-header-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    let modal = '#kpiHeaderModal'
                    if (data) {
                        $(modal + ' input[name="id"]').val(data.id);
                        $(modal + ' input[name="name"]').val(data.name);
                        $(modal + ' input[name="is_active"]').prop('checked',data.is_active);
                        $(modal).modal();
                        $(modal + ' .modal-title').text("Edit Header: " + data.name)
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        },
        onSaveForm() {
            let root = this;
            let _form = $('#kpi-header-form');
            let message = 'Header has been created successfully';
            if ($('#kpi-header-form input[name="id"]').val()) {
                message = 'Header has been updated successfully';
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('admin.kpi-header.store')}}",
                type: 'POST',
                data: root.collectHeaderData(),
                success: function(data) {
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: message,
                            type: "success",
                            icon: "success",
                            button: "Ok",

                        }, function() {
                            $('#kpiHeaderModal').modal('hide');
                            root.data.kpiHeaderTable.ajax.reload();
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
        init() {
            let root = this;
            this.loadKpiHeaderTable();

            //On add new field
            $(".js-new-kpi-header-btn").on('click', function(e) {
                root.resetHeaderModal();
                $('#kpiHeaderModal').modal('show');
            });

            $(".js-kpih-cancel").on('click', function(e) {
                $('#kpiHeaderModal').modal('hide');
            });

            //On delete header
            $('#table-kpi-headers').on('click', '.js-kpih-delete', function(e) {
                root.onDeleteHeader(this);
            });

            //On edit header
            $('#table-kpi-headers').on('click', '.js-kpih-edit', function(e) {
                root.onEditHeader(this);
            });

            //Save or update kpi header
            $('.js-kpih-save').on('click', function(e) {
                e.preventDefault();
                root.onSaveForm();
            });
        }
    }


    $(function() {
        kpiHeaderScript.init();
    });
</script>
@stop