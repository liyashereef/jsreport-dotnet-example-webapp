@extends('adminlte::page')
@section('title', 'Kpi Header Allocation')
@section('content_header')
<h1>KPI Header Allocation</h1>
@stop

@section('css')
<style>
    .js-kpih-allocation-new {
        float: right;
    }
</style>
@stop

@section('content')

<div class="row" style="margin-top: 10px;margin-bottom: 10px;">
    <div class="col-md-10">
        <!-- <h4 class="kpi-title"></h4> -->
    </div>
    <div class="col-md-2">
        <div class="add-new js-kpih-allocation-new">Allocate KPI</div>
    </div>
</div>

<div class="">
    <table id="table-kpih-allocation" class="table table-bordered" style="text-align: center; width:100%;">
        <thead>
            <tr>
                <th class="sorting_disabled">#</th>
                <th class="sorting_disabled">Header Name</th>
                <th>KPI</th>
                <th>Status</th>
                <th>Thresholds</th>
                <th></th>
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="kpiAllocationModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4>KPI Allocation</h4>
                    </div>
                    <div class="col-md-6">
                        <button type="button" data-dismiss="modal" aria-label="Close" style="float: right">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
            </div>
            <form id="kpi-allocation-form">
                <div class="modal-body">
                    <input type="hidden" name="id" value="" />
                    <div class="row">
                        <div class="col-md-4">
                            <label>Header</label>
                        </div>
                        <div class="col-md-6">
                            <select name="kpi_customer_header_id" id="kpiha-header-select" class="form-control"></select>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>KPI</label>
                        </div>
                        <div class="col-md-6">
                            <select name="kpi_master_id" id="kpiha-kpi-select" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-4">
                            <label>Threshold Type</label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="threshold-type form-control" readonly />
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-4">
                            <label>Active</label>
                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" name="is_active" value="1" checked />
                        </div>
                    </div>
                    <div id="threshold-section" style="margin-top:10px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary blue js-kpih-allocation-cancel">Cancel</button>
                    <button type="button" class="btn btn-primary blue js-kpih-allocation-save">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>
@stop

@section('js')
<script>
    const kpiAllocationScript = {
        data: {
            kpiAllocationTable: null,
            settings: {},
            unusedKpiList: [],
            allocEditData: null,
        },
        resetAllocationModal() {
            let modal = '#kpiAllocationModal'
            $(modal + ' input[name="id"]').val('');
            $(modal + ' input[name="name"]').val('');
            $(modal + ' input[name="is_active"]').val('1');
            $(modal + ' .modal-title').text("Allocate");
        },
        resolveThresholdText(threshold) {
            if (threshold == 1) {
                return 'Rating'
            }
            if (threshold == 2) {
                return 'Percentage';
            }
            return '';
        },
        setThresholdText(thresholdId) {
            let ts = this.resolveThresholdText(thresholdId);
            //Set threshold type
            $(`#kpi-allocation-form .threshold-type`).val(ts);

        },
        populateAllocationModal(data) {
            let modal = '#kpiAllocationModal';
            let thresholdEls = '';
            let _isEditMode = data != null ? true : false;
            let thresholdId = null;

            const input = {
                id: '',
                kpi_customer_header_id: '',
                kpi_master_id: '',
                is_active: ''
            };

            if (_isEditMode) {
                this.data.allocEditData = data;
                input.id = data.id;
                input.kpi_customer_header_id = data.kpi_customer_header_id
                input.kpi_master_id = data.kpi_master_id;
                input.is_active = data.is_active;
                thresholdId = data.kpi_master.threshold_type;
            } else {
                this.data.allocEditData = null;
            }
            if (input.is_active == 1) {
                $(`#kpi-allocation-form input[name="is_active"]`).prop("checked", true);
            } else {
                $(`#kpi-allocation-form input[name="is_active"]`).prop("checked", false);
            }
            //Populate Headers
            if (Array.isArray(this.data.settings.headers)) {
                let _hdrOptions = '<option value="">Select Header</option>';
                this.data.settings.headers.forEach(function(_hdr, index) {
                    let _isc = (_isEditMode && data.kpi_customer_header_id == _hdr.id) ? 'selected' : '';
                    _hdrOptions += `<option value="${_hdr.id}" ${_isc} >${_hdr.name}</option>`;
                });

                $('#kpiha-header-select').children().remove().end().append(_hdrOptions);
            }

            //Populate thresholds
            if (Array.isArray(this.data.settings.colors)) {
                this.data.settings.colors.forEach(function(_color, index) {
                    let _tid = '';
                    let _min = '';
                    let _max = '';
                    if (_isEditMode) {
                        let _result = data.kpi_thresholds.filter(function(kt) {
                            return kt.kpi_threshold_color_id === _color.id;
                        });

                        if (_result.length > 0) {
                            let _th = _result[0]
                            _tid = _th.id;
                            _min = _th.min_val_fmt;
                            _max = _th.max_val_fmt;
                        }
                    }

                    thresholdEls += `
                    <div class="row" style="margin-top:10px;">
                        <input type="hidden" name="color_id[]" value="${_color.id}" />
                        <input type="hidden" name="threshold_id[]" value="${_tid}" />
                        <div class="col-md-4">
                            <label>Threshold (${_color.color})</label>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Min:</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" value="${_min}" min="0" max="100"  class="form-control tval tmin" name="min[]" />
                                </div>

                                <div class="col-md-2">
                                    <label>Max:</label>
                                </div>
                                <div class="col-md-4">
                                   <input type="number"value="${_max}" min="0" max="100" class="form-control tval tmax" name="max[]" />
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                });

                $('#threshold-section').html(thresholdEls);
                $('#kpiha-header-select').change();
            }

            //Set values
            for (const [key, value] of Object.entries(input)) {
                $(modal + ` input[name="${key}"]`).val(value);
            }
            //Set threshold type label
            this.setThresholdText(thresholdId);

        },
        populateKpiDropdown(data) {
            let root = this;
            let dt = root.data.allocEditData;
            if (Array.isArray(data)) {
                let _els = '<option value="">Select KPI</option>';
                data.forEach(function(_item, index) {
                    let _isc = (dt != null && dt.kpi_master_id == _item.id) ? 'selected="selected"' : '';
                    _els += `<option value="${_item.id}" ${_isc} data-tid="${_item.threshold_type}">${_item.name}</option>`;
                });
                $('#kpiha-kpi-select').children().remove().end().append(_els);
            }
        },
        onChangeHeaderDropdown(el) {
            let root = this;
            let baseUrl = '{{ route("admin.kpi.customer-allocation.unset-list",":id") }}';
            let url = baseUrl.replace(':id', $(el).val());
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    root.populateKpiDropdown(data);
                },
                contentType: false,
                processData: false,
            });
        },
        collectModalData() {
            let formData = new FormData($('#kpi-allocation-form')[0]);
            let isActive = $(`#kpi-allocation-form input[name="is_active"]`).is(":checked");
            if (isActive) {
                formData.set('is_active', 1);
            } else {
                formData.set('is_active', 0);
            }
            return formData;
        },
        fetchSettings() {
            let root = this;
            let url = '{{ route("admin.kpi.customer-allocation.settings") }}';

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    root.data.settings = data;
                },
                contentType: false,
                processData: false,
            });
        },
        loadKpiAllocationTable() {
            $.fn.dataTable.ext.errMode = 'throw';
            var listUrl = '{{ route("admin.kpi.customer-allocation.list") }}';
            this.data.kpiAllocationTable = $('#table-kpih-allocation').DataTable({
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
                        data: null,
                        sortable: true,
                        render: function(o) {
                            return o.kpi_customer_header.name;
                        },
                    },
                    {
                        data: null,
                        sortable: true,
                        render: function(o) {
                            return o.kpi_master.name
                        },
                    },
                    {
                        data: null,
                        sortable: true,
                        render: function(o) {
                            let activeLabel = o.is_active ? 'Active' : 'Inactive';
                            return `<p>${activeLabel}</p>`
                        },
                    },
                    {
                        data: null,
                        sortable: true,
                        render: function(o) {
                            let _thresholdEls = '';
                            if (o.kpi_thresholds != null) {
                                o.kpi_thresholds.forEach(function(kt, i) {
                                    let t = kt.kpi_threshold_color;
                                    _thresholdEls += `<p>${kt.min_val_fmt} - ${kt.max_val_fmt} (${t.color})</p>`;
                                });
                            }
                            return _thresholdEls;
                        },
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            let actions = '';
                            @can('edit_masters')
                            actions += '<a href="javascript:void(0)" class="edit js-kpih-allocation-edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<a href="javascript:void(0)" class="delete js-kpih-unallocate fa fa-minus-circle" data-id=' + o.id + '></a>';
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        },
        onUnallocate(el) {
            let root = this;
            let id = $(el).data('id');
            let base_url = "{{ route('admin.kpi.customer-allocation.destroy',':id') }}";
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
                                swal("Unallocated", 'KPI header unallocated', "success");
                                root.data.kpiAllocationTable.ajax.reload();
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
        onEditAllocation(el) {
            let root = this;
            let id = $(el).data('id');
            let baseUrl = '{{ route("admin.kpi.customer-allocation.single",":id") }}';
            let url = baseUrl.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    root.populateAllocationModal(data);
                    $('#kpiAllocationModal').modal('show');
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
            });
        },
        checkOverlap() {
            let result = {
                status: true,
                message: ''
            };

            let mins = $('.tmin').map(function(idx, elem) {
                return $(elem).val();
            }).get();
            let maxs = $('.tmax').map(function(idx, elem) {
                return $(elem).val();
            }).get();

            for (let i = 0; i < mins.length; i++) {
                let _min = Number(mins[i]);
                let _max = Number(maxs[i])
                if (_min >= _max) {
                    result.status = false;
                    result.message = 'Minimum threshold should be less than maximum threshold';
                    return result;
                }
                //Check for out of range
                if (_min < 0 || _max < 0) {
                    result.status = false;
                    result.message = 'Threshold values should not be less than zero';
                    return result;
                }
                if (_max > 100 || _max > 100) {
                    result.status = false;
                    result.message = 'Threshold values should not be greater than 100';
                    return result;
                }
            }

            //Check for overlap
            return result;

        },
        onSaveForm() {
            let root = this;

            //Check empty thresholds
            let ts = $(".tval");
            for (let x of ts) {
                if ($(x).val() == "") {
                    return swal("Alert", "Thresholds should not be empty", "warning");
                }
            }
            //Check threshold overlapping
            let oc = this.checkOverlap();
            if (oc.status == false) {
                return swal("Alert", oc.message, "warning");
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('admin.kpi.customer-allocation.store')}}",
                type: 'POST',
                data: root.collectModalData(),
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: "Header allocated successfully",
                            type: "success",
                            icon: "success",
                            button: "Ok",

                        }, function() {
                            $('#kpiAllocationModal').modal('hide');
                            root.data.kpiAllocationTable.ajax.reload();
                        });
                    } else {
                        if (data.error) {
                            swal("Alert", data.error, "warning");
                        } else {
                            swal("Alert", "Something went wrong", "warning");
                        }
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Oops", "Something went wrong", "warning");
                },
            });
        },
        init() {
            let root = this;
            this.fetchSettings();
            this.loadKpiAllocationTable();

            //On add new field
            $(".js-kpih-allocation-new").on('click', function(e) {
                root.resetAllocationModal();
                root.populateAllocationModal();
                $('#kpiAllocationModal').modal('show');
            });

            //On allocation cancel
            $(".js-kpih-allocation-cancel").on('click', function(e) {
                $('#kpiAllocationModal').modal('hide');
            });

            //On change header
            $("#kpiha-header-select").change(function() {
                root.onChangeHeaderDropdown(this);
            });

            //On change KPI
            $("#kpiha-kpi-select").change(function() {
                root.setThresholdText($(this).find(':selected').data('tid'));
            });

            //On delete header
            $('#table-kpih-allocation').on('click', '.js-kpih-unallocate', function(e) {
                root.onUnallocate(this);
            });

            //On edit allocation
            $('#table-kpih-allocation').on('click', '.js-kpih-allocation-edit', function(e) {
                root.onEditAllocation(this);
            });

            //Save or update allocation
            $('.js-kpih-allocation-save').on('click', function(e) {
                e.preventDefault();
                root.onSaveForm();
            });
        }
    }

    $(function() {
        kpiAllocationScript.init();
    });
</script>
@stop