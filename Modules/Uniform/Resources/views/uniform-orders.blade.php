@extends('layouts.app')

@section('content')

<style type="text/css">
    table td,
    table th {
        text-align: center !important;
    }

    .table-order-item thead th {
        background: #1f5e8c;
    }

    .table-order-item tbody td {
        background: #efdedb;
        border: solid 1px #f1c7bf !important
    }

    .fence-title {
        font-style: italic;
        font-size: 16px;
    }

    .fence-details-section table th {
        background-color: #fdd5c3;
    }

    .fac-disabled {
        color: #a2a2a2 !important;
    }
</style>
<div class="table_title">
    <h4>Uniform Orders</h4>
</div>
<div class="urat">
    <table class="table table-bordered" id="uniform-order-table">
        <thead>
            <tr>
                <th width="1%">#</th>
                <th width="10%">Employee</th>
                <th width="10%">Site</th>
                <th width="10%">Total Cost</th>
                <th>Ura Deducted</th>
                <th width="10%">Shipping Address</th>
                <th>Processed By</th>
                <th>Notes</th>
                <th>Last Updated</th>
                <th>Updated At</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div id="uniform-stat-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form id="uniform-stat-form">
            {{ Form::hidden('id', null) }}
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Order Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group" id="status">
                        <label for="order-status" class="col-sm-3 control-label">Status </label>
                        <div class="col-sm-11">
                            <select class="form-control select2 stats-dropdown" name="status" id="order-status"></select>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group" id="notes">
                        <label for="note" class="col-sm-3 control-label">Note </label>
                        <div class="col-sm-11">
                            {!!Form::textarea('notes',null, ['class' => 'form-control','id'=>'note','rows'=>'3','placeholder' => 'Notes'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group " id="email_script_block">
                        <label for="email_script" class="col-sm-12 control-label">Email Script</label>
                        <div class="col-sm-11">
                            {{Form::textarea('email_script',null,array('class'=>'form-control ckeditor','id'=>'editor'))}}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group row" style="margin-left:0px;margin-bottom:2px;">
                        <label for="is_email_required" class="col-sm-3">Is Email Required</label>
                        <div class="col-sm-8">
                            {{ Form::checkbox('is_email_required',null,'checked', array('class'=>'form-control','id'=>'is_email_required','style'=>'width:22px;height:30px;')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class='button btn btn-primary blue' id="order-stat-save">Save</button>
                    <button type="button" class='button btn btn-primary blue' id="order-stat-cancel" data-dismiss='modal'>Cancel</button>
                </div>
            </div>
            </from>

    </div>
</div>
@stop
@section('scripts')
<script>
    $('#order-status').on('change', function() {
        var id = $(this).val();
        if (id) {
            var base_url = "{{route('uniform.email-script.single',':id')}}";
            var url = base_url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data.email_body) {
                        CKEDITOR.instances['editor'].setData(data.email_body)
                    }else{
                        swal("Alert", "Template not set", "warning");
                        CKEDITOR.instances['editor'].setData('')
                    }
                }
            });
        } else {
            CKEDITOR.instances['editor'].setData('')
        }
    });

    // $('input[name="is_email_required"]').on("click", function(event) {
    //     if (this.checked == true) {
    //         $("#email_script_block").show();
    //     } else {
    //         $("#email_script_block").hide();
    //     }
    // });

    const urat = {
        table: null,
        init() {
            let root = this;

            $('body ').on('click', '.order-stat-edit', function() {
                root.onUniformStatusEdit(this);
            });

            $('#order-stat-save').click(function() {
                root.onUniformUpdateStatus();
            });

            $('#order-stat-cancel').click(function() {
                root.onUniformOrderReset();
            });

            this.initTable();


            //show geolocation summary table
            $('#uniform-order-table').on('click', 'td.details-control', function() {
                let id = $(this).closest('tr').find('.buttons').data('id');
                let tr = $(this).closest('tr');
                let row = root.table.row(tr);

                if (row.child.isShown()) {
                    tr.find('td.details-control').html('<button  class="btn fa fa-plus-square buttons" data-id=' + id + '></button>');
                    tr.removeClass('shown');
                    row.child.hide();
                } else {
                    let view_url = '{{ route("uniform.orders.items",":id") }}';
                    view_url = view_url.replace(':id', id);
                    $.ajax({
                        type: 'GET',
                        url: view_url,
                        dataType: 'json',
                        success: function(data) {
                            tr.find('td.details-control').html('<button  class="btn fa fa-minus-square buttons"  data-id=' + id + '></button>')
                            tr.addClass('shown');
                            row.child(root.generateItemsTable(data)).show();
                        },
                        error: function() {}
                    });
                }
                // refreshSideMenu();
            });
        },
        generateItemsTable(data) {
            var html = '';
            $.each(data, function(key, item) {
                html += `
                <tr>
                    <td>${key+1}</td>
                    <td>${item.product_name }</td>
                    <td>${item.variant_name }</td>
                    <td>$${item.product_selling_price }</td>
                    <td>${item.product_quantity}</td>
                    <td>$${item.net_amount }</td>
                    <td>${item.tax_rate }%</td>
                    <td>${item.tax_type }</td>
                    <td>$${item.tax_amount }</td>
                    <td>$${item.product_total_cost}</td>
                </tr>
            `;
            });

            return `<table  class="table table-order-item">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Net Amount</th>
                            <th>Tax Rate</th>
                            <th>Tax Type</th>
                            <th>Tax Amount</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                <tbody class="child_elements">${html}</tbody>
            </table>`;
        },
        onUniformStatusEdit(el) {
            let _id = $(el).data('id');
            let _url = '{{ route("uniform.orders.single",":id") }}';
            _url = _url.replace(':id', _id);

            $('#work-hour-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: _url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        if (data.template) {
                            CKEDITOR.instances['editor'].setData(data.template.email_body)
                        } else {
                            CKEDITOR.instances['editor'].setData('')
                        }

                        $('input[name="is_email_required"]').prop("checked", true);
                        $("#email_script_block").show();


                        $('#uniform-stat-modal input[name="id"]').val(data.item.id);
                        //Set select dropdown
                        let _els = '<option value="">Please Select</option>';
                        data.status.forEach(function(d) {
                            if (data.item.status_log.length > 0) {
                                let sl = data.item.status_log[0];
                                let _selected = (sl && d.machine_code == sl.order_status.machine_code) ? 'selected' : '';
                                _els += `<option value="${d.machine_code}" ${_selected}>${d.display_name}</option>`
                            } else {
                                _els += `<option value="${d.machine_code}">${d.display_name}</option>`
                            }
                        });
                        $('.stats-dropdown').empty().append(_els)
                        $('#uniform-stat-modal').modal('show');

                    } else {
                        swal("Error", "Something went wrong");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    swal("Error", "Something went wrong");
                },
                contentType: false,
                processData: false,
            });

        },
        onUniformUpdateStatus() {
            let root = this;
            let _form = $('#uniform-stat-form');
            let _data = new FormData($('#uniform-stat-form')[0]);
            CKEDITOR.instances.editor.updateElement();
            var editor = (CKEDITOR.instances.editor.getData());
            _data.append('email_script', editor);
            _form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('uniform.orders.update-status') }}",
                type: 'POST',
                data: _data,
                success: function(data) {
                    if (data.success) {
                        swal("Saved", "Order status has been updated", "success");
                        root.table.ajax.reload();
                        root.onUniformOrderReset();
                    } else {
                        swal('Error', data.message, 'error');
                    }
                },
                fail: function(response) {},
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, _form);
                },
                contentType: false,
                processData: false,
            });
        },
        onUniformOrderReset() {
            this.clearForm();
            $('#uniform-stat-modal').modal('hide');
        },
        clearForm() {
            $("#uniform-stat-form")[0].reset();
        },

        initTable() {
            let root = this;
            root.table = $('#uniform-order-table').DataTable({
                bProcessing: false,
                responsive: false,
                dom: 'lfrtip',
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('uniform.orders.list') }}",
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                "aaSorting": [[9,'desc']],

                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                "columnDefs" : [{"targets":8, "type":"date-eu","visible":false}],

                columns: [{
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return '<button data-id="' + data.id + '" class="btn fa fa-plus-square buttons"></button>';
                        },
                        orderable: false,
                        className: 'details-control',
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'employee_name_no',
                        name: 'employee_name_no'
                    },
                    {
                        data: 'site_name_no',
                        name: 'site_name_no'
                    },
                    {
                        data: 'total_cost',
                        name: 'total_cost'
                    },
                    {
                        data: 'ura_deducted',
                        render: function(o) {
                            return '$' + o;
                        }
                    },

                    {
                        data: 'shipping_address',
                        name: 'shipping_address'
                    },
                    {
                        data: 'statusLogArr.[ <br><br>].taken_by',
                        name: 'statusLogArr.0.taken_by'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            var notes_str = notes_str1 = new_notes_str = "";
                            for (var i = 0; i < data.statusLogArr.length; i++) {
                                if (data.statusLogArr[i].notes == "") {
                                    notes_str = notes_str + '<span style="display:none"></span><br/><br/>\r\n';
                                } else {
                                    notes_str1 = data.statusLogArr[i].notes;
                                    if (notes_str1.length > 35) {
                                        notes_str = notes_str + '<span class="show-btn nowrap" onclick="$(this).hide();$(this).next().show();">' + notes_str1.substr(0, 40) + '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a></span><span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' + notes_str1 + '&nbsp;&nbsp;<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a></span><br/><br/>\r\n';
                                    } else {
                                        notes_str = notes_str + '<span>' + notes_str1 + '</span><br/><br/>\r\n';
                                    }
                                }
                            }
                            return notes_str;
                        },
                        name: 's8tatusLogArr.0.notes'
                    },
                    {
                        data: null,
                        name: 'statusLogArr.0.updated_at',
                        render:function(data, type, row){
                            let lastIndex=data.statusLogArr.length-1;
                            // let lastDatestring=Date.parse(data.statusLogArr[lastIndex].updated_at).toLocaleDateTimeString({"timeZone":"America/Phoenix", "format":"yyyyMMddHHmm"}).
                            var d = new Date(data.statusLogArr[0].updated_at);
                            // return d;
                            return moment(data.statusLogArr[0].updated_at).format('D/MM/YYYY HH:mm:ss');
                        }
                    },
                    {
                        data: 'statusLogArr.[ <br><br>].updated_at',
                        name: 'statusLogArr.0.updated_at'
                    },
                    {
                        data: 'statusLogArr.[ <br><br>].status',
                        name: 'statusLogArr.0.status'
                    },
                    {
                        data: null,
                        sortable: true,
                        render: function(o) {
                            @can('change_uniform_order_status')
                            return '<a href="#" class="order-stat-edit fa fa-pencil" data-id=' + o.id + '></a>';
                            @endcan
                            return null;
                        },
                    },

                ],
            });
        }

    };

    //Document ready init
    $(function() {
        urat.init();
    });
</script>

@stop
