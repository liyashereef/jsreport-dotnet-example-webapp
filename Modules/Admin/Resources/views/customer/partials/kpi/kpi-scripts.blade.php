<script>
    const kpiUtils = {
        getCustomerId() {
            return $('#id').val();
        },
    }

    const kpiCustGroupScript = {
        data: {
            table: null,
        },
        populateGroupDropdown(data) {
            let root = this;
            if (Array.isArray(data)) {
                let _els = `<option value="">Please Select</option>`;
                data.forEach(function(_item, index) {
                    _els += `<option value="${_item.id}">${_item.name}</option>`;
                });
                $('#kpi-cust-group-select').children().remove().end().append(_els);
            }
        },
        fetchGroups() {
            let root = this;
            let url = '{{ route("admin.kpi.groups.leaf-nodes") }}';

            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    root.populateGroupDropdown(res);
                },
                contentType: false,
                processData: false,
            });
        },
        populateKpiDropdown(data) {
            let root = this;
            if (Array.isArray(data)) {
                let _els = `<option value="#">Please Select</option>`;
                data.forEach(function(_item, index) {
                    _els += `<option value="${_item.id}">${_item.name}</option>`;
                });
                $('#kpi-allocation-select').children().remove().end().append(_els);
            }
        },
        fetchKpis() {
            let root = this;
            let url = '{{ route("admin.kpi.list") }}';

            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    root.populateKpiDropdown(res.data);
                },
                contentType: false,
                processData: false,
            });
        },
        loadKpiAllocationTable() {
            $.fn.dataTable.ext.errMode = 'throw';
            var base_url = '{{ route("admin.kpi-customer-allocation.list",":customer_id") }}';
            let listUrl = base_url.replace(':customer_id', kpiUtils.getCustomerId());

            this.data.kpiAllocationTable = $('#table-kpi-allocation').DataTable({
                bProcessing: false,
                responsive: true,
                ajax: listUrl,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        sortable: false
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            return o.kpi_master.name;
                        },
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            return o.created_at
                        },
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            let actions = '';
                            @can('lookup-remove-entries')
                            actions += '<a href="javascript:void(0)" class="delete js-unallocate fa fa-minus-circle" data-id=' + o.id + '></a>';
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        },
        loadGroupAllocationTable() {
            $.fn.dataTable.ext.errMode = 'throw';
            var base_url = '{{ route("admin.kpi.customer-group.list",":id") }}';
            let listUrl = base_url.replace(':id', kpiUtils.getCustomerId());
            this.data.table = $('#table-kpi-cust-group').DataTable({
                bProcessing: false,
                responsive: true,
                ajax: listUrl,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        sortable: false
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            return o.group.name;
                        },
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            return o.created_at
                        },
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            let actions = '';
                            @can('lookup-remove-entries')
                            actions += '<a href="javascript:void(0)" class="delete js-unallocate fa fa-minus-circle" data-id=' + o.id + '></a>';
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
            let base_url = "{{ route('admin.kpi.customer-group.destroy',':id') }}";
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
                                swal("Unallocated", data.message, "success");
                                root.data.table.ajax.reload();
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
        onUnallocateKPI(el) {
            let root = this;
            let id = $(el).data('id');
            let base_url = "{{ route('admin.kpi-customer-allocation.destroy',':id') }}";
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
                                swal("Unallocated", data.message, "success");
                                // root.data.table.ajax.reload();
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
        onSaveForm() {
            let root = this;
            let _selGroup = $('#kpi-cust-group-select').val();
            let _cid = kpiUtils.getCustomerId();

            // //Skip execution if no el selected
            // if (_selGroup === "") {
            //     return;
            // }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('admin.kpi.customer-group.store')}}",
                type: 'POST',
                data: JSON.stringify({
                    "kpi_group_id": _selGroup,
                    "customer_id": _cid
                }),
                dataType: "json",
                contentType: "application/json; charset=utf-8",

                processData: false,
                success: function(data) {
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: "Group allocated successfully",
                            type: "success",
                            icon: "success",
                            button: "Ok",

                        }, function() {
                            root.data.table.ajax.reload();
                        });
                    } else {
                        swal("Alert", data.message, "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Alert", xhr.responseJSON.message, "warning");
                },
            });
        },
        onSaveKpiAllocation() {
            let root = this;
            let _selKpi = $('#kpi-allocation-select').val();
            let _cid = kpiUtils.getCustomerId();

            //Skip execution if no el selected
            if (_selKpi === "") {
                return;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('admin.kpi.customer.allocation')}}",
                type: 'POST',
                data: JSON.stringify({
                    "kpi_master_id": _selKpi,
                    "customer_id": _cid
                }),
                dataType: "json",
                contentType: "application/json; charset=utf-8",

                processData: false,
                success: function(data) {
                    if (data.success) {
                        swal({
                            title: 'Success',
                            text: "KPI allocated successfully",
                            type: "success",
                            icon: "success",
                            button: "Ok",

                        }, function() {
                            root.data.kpiAllocationTable.ajax.reload();
                        });
                    } else {
                        swal("Alert", data.message, "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Alert", xhr.responseJSON.message, "warning");
                },
            });
        },
        init() {
            let root = this;
            this.fetchGroups();
            this.fetchKpis();
            this.loadKpiAllocationTable();
            this.loadGroupAllocationTable();

            //On delete header
            $('#table-kpi-cust-group').on('click', '.js-unallocate', function(e) {
                root.onUnallocate(this);
            });

            //Save or update allocation
            $('.js-cust-group-save').on('click', function(e) {
                e.preventDefault();
                root.onSaveForm();
            });

              //Save or update kpi allocation
            $('.js-kpi-allocatopn-save').on('click', function(e) {
                e.preventDefault();
                root.onSaveKpiAllocation();
            });
            //On delete kpi
            $('#table-kpi-allocation').on('click', '.js-unallocate', function(e) {
                root.onUnallocateKPI(this);
            });

        }
    }

    $(function() {
        kpiCustGroupScript.init();
    });
</script>
