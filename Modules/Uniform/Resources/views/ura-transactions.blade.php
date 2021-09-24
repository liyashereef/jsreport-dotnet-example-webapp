@extends('layouts.app')

@section('content')
<style>
    .row-fix {
        margin: 2px 0px;
    }

    .add-new {
        margin-top: 0px;
    }

    .bal-gr {
        color: green;
    }

    .bal-rd {
        color: red;
    }

    .amount {
        font-weight: bold;

    }

    .ura-refresh-btn {
        margin-left: 20px;
    }

    .title-block {
        display: inline-block;
        width: 100%;
    }

    .transaction-title {
        display: inline;
    }

    #ura-transactions-table td,
    #ura-transactions-table th {
        text-align: center;
    }

    .ura-revoked-tr {
        background-color: #f7f7f7 !important;
    }

    .ura-tr-crd .ura-amt-td {
        color: green;
    }

    .ura-tr-deb .ura-amt-td {
        color: red;
    }
</style>

<div class="table_title">
    <h4>URA Transactions</h4>
</div>
<div class="urat">
    <div class="row mb-3">
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-2"><label class="filter-text">Employee</label></div>
                <div class="col-md-4">
                    <select class="form-control option-adjust user-filter select2" name="user-filter" id="user-filter">
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                        <option value="{{$user['id']}}">{{$user['emp_name']}} ({{$user['emp_no']}})</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
                <div class="col-md-6">
                    @can('view_ura_balance')
                    <div class="row js-bal-inf">
                        <div class="col-md-4">
                            <div class="ura-bal">
                                URA Balance: <span class="amount bal-gr">$0.00</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ura-ern">
                                URA Earned: <span class="amount bal-rd">$0.00</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ura-ern">
                                URA Hours: <span class="ura-hrs">0.00</span>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col-md-2">
            @if(Gate::check('add_ura_debit_transaction') || Gate::check('add_ura_credit_transaction'))
            <button type="button" class="btn btn-lg add-new">New Transaction</button>
            @endif
        </div>
    </div>
    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3 title-block">
                <h5 class="transaction-title"></h5>
                <a href="javascript:void(0)" class="ura-refresh-btn"><i class="fa fa-lg fa-refresh" aria-hidden="true"></i></a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="pull-right mr-2">
                <input id="hide_revoked" type="checkbox" checked="checked">
                <label class="ml-1"> Hide Revoked Transactions</label>
            </div>
        </div>
    </div>


    <table class="table table-bordered" id="ura-transactions-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Transaction Date</th>
                <th>Transaction Type</th>
                <th>Employee Details</th>
                <th>Operation</th>
                <th>Hours</th>
                <th>Rate Applied</th>
                <th>URA</th>
                <th>Balance</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div id="urat-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form id="urat-form">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group" id="amount">
                        <label for="first_name" class="col-sm-3 control-label">Amount<span class="mandatory">*</span></label>
                        <div class="col-sm-11">
                            {{ Form::number('amount','',['class' => 'form-control','id'=>'amount','placeholder' => '0.00']) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="transaction_type">
                        <div class="row row-fix">
                            <div class="col-md-4">
                                <label for="first_name" class="control-label">Transaction Type<span class="mandatory">*</span></label>
                            </div>
                            <div class="col-md-8">
                                @can('add_ura_debit_transaction')
                                <input type="radio" id="trt-debit" name="transaction_type" value="1">
                                <label for="trt-debit">DEBIT</label>
                                @endcan

                                @can('add_ura_credit_transaction')
                                <input type="radio" id="trt-credit" class="ml-4" name="transaction_type" value="2">
                                <label for="trt-credit">CREDIT</label>
                                @endcan
                            </div>
                        </div>
                        <div class="col-md-11">
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="ura_operation_id">
                        <label for="operation-field" class="col-sm-3 control-label">Operation<span class="mandatory">*</span></label>
                        <div class="col-sm-11">
                            {!!Form::select('ura_operation_id', [null=>'Select Operation'] + $uraOperationTypes,null, ['class' => 'form-control','id'=>'operation-field'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>


                    <div class="form-group" id="notes">
                        <label for="note" class="col-sm-3 control-label">Note </label>
                        <div class="col-sm-11">
                            {!!Form::textarea('notes',null, ['class' => 'form-control','id'=>'note','rows'=>'3'])!!}
                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class='button btn btn-primary blue' id="urat-save">Save</button>
                    <button type="button" class='button btn btn-primary blue' id="urat-cancel" data-dismiss='modal'>Cancel</button>
                </div>
            </div>
            </from>

    </div>
</div>
@stop
@section('scripts')
<script>
    const urat = {
        table: null,
        init() {
            let root = this;

            $('#user-filter').select2();

            $('.add-new').click(function() {
                root.onNewTransaction();
            });

            $('#urat-save').click(function() {
                root.onTransactionSave();
            });

            $('#urat-cancel').click(function() {
                root.onTransactionReset();
            });

            $('#user-filter').on('change', function() {
                root.onChangeUserFilter();
            });

            $('.ura-refresh-btn').on('click', function(e) {
                e.preventDefault();
                root.onUraManualRefresh();
            });

            $('#hide_revoked').on('click', function(e) {
                root.table.ajax.reload();
            });
            this.initTable();
            $("#user-filter").trigger('change');
        },
        onUraManualRefresh() {
            this.table.ajax.reload();
            this.getUpdatedUraBalance();
        },
        resetBalanceInfo() {
            $('.ura-ern .amount').html(this.asCurrency(0.00));
            $('.ura-bal .amount').html(this.asCurrency(0.00));
            $('.ura-hrs').html(0.00);
        },
        getUpdatedUraBalance() {
            let root = this;
            let userId = $("#user-filter").val();

            //No user selected
            if (!userId) {
                root.resetBalanceInfo();
                return;
            }

            $.get({
                url: "{{ route('ura.balance.info') }}",
                type: "GET",
                global: false,
                timeout: 5000,
                data: {
                    'user_id': userId
                },
                success: function(data) {
                    $('.ura-ern .amount').html(root.asCurrency(root.toFixedFmt(data.ura_earned)));
                    $('.ura-bal .amount').html(root.asCurrency(root.toFixedFmt(data.ura_balance)));
                    $('.ura-hrs').html(root.toFixedFmt(data.ura_hours));
                },
                fail: function(response) {
                    root.resetBalanceInfo();
                }
            });
        },
        updateTransactionTitle() {
            let _out = 'Recent Transactions';
            let _uid = $("#user-filter").val();

            if (_uid) {
                _out = 'Transactions of ' + $("#user-filter option:selected").text();
            }
            $('.transaction-title').html(_out);
        },
        onChangeUserFilter() {
            let userId = $("#user-filter").val();
            if (userId) {
                $('.js-bal-inf').show();
            } else {
                $('.js-bal-inf').hide();
            }
            this.getUpdatedUraBalance();
            this.table.ajax.reload();
        },
        onNewTransaction() {
            this.clearTransactionForm();
            if (this.checkUserSelected()) {
                $('#urat-modal').modal()
            }
        },
        checkUserSelected() {
            let userId = $("#user-filter").val();
            //Validate user selection
            if (!userId) {
                swal("Warning", "Select an employee first", "warning");
                return false;
            }
            return true;
        },
        onTransactionSave() {
            let root = this;
            let _form = $('#urat-form');
            let userId = $("#user-filter").val();

            if (!this.checkUserSelected()) {
                return; //skip
            }

            let data = new FormData($('#urat-form')[0]);
            data.set('user_id', userId);

            _form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('ura.transactions.new') }}",
                type: 'POST',
                data: data,
                success: function(data) {
                    if (data.success) {
                        swal("Saved", "Transaction has been saved", "success");
                        root.table.ajax.reload();
                        root.onTransactionReset();
                        root.getUpdatedUraBalance();
                    } else {
                        let msg = (data.message !== null || data.message !== undefined) ?
                            data.message : "Transaction not saved";

                        swal("Error", msg, "error");
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
        onTransactionReset() {
            this.clearTransactionForm();
            $('#urat-modal').modal('hide');
        },
        clearTransactionForm() {
            $("#urat-form")[0].reset();
        },
        toFixedFmt(val) {
            if (isNaN(val)) {
                return val;
            }
            return parseFloat(val).toFixed(2);
        },
        asCurrency(val) {
            return '$' + this.toFixedFmt(val);
        },
        initTable() {
            let root = this;
            this.table = $('#ura-transactions-table').DataTable({
                responsive: true,
                dom: 'Blfrtip',
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('ura.transactions.list') }}",
                    "data": function(d) {
                        root.updateTransactionTitle();
                        d.user_id = $("#user-filter").val();
                        if ($('#hide_revoked').is(":checked")) {
                            d.hide_revoked = 1;
                        }
                    },
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                "rowCallback": function(row, data) {
                    if (data.revoked == 1) {
                        $(row).addClass('ura-revoked-tr');
                    }
                    let docClass = data.transaction_type == "CREDIT" ? 'ura-tr-crd' : 'ura-tr-deb';
                    $(row).addClass(docClass);
                },
                "drawCallback": function(settings, json) {
                    $(".note-pop").popover({
                        trigger: "hover"
                    });
                },
                buttons: [{
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-pdf-o',
                    },
                    {
                        extend: 'excelHtml5',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-excel-o'
                    },
                    // {
                    //     extend: 'print',
                    //     pageSize: 'A2',
                    //     //text: ' ',
                    //     //className: 'btn btn-primary fa fa-print'
                    // },
                ],
                order: [
                    [0, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(o) {
                            return moment(o).format('DD-MM-YYYY HH:mm A')
                        }
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'user.full_name',
                        sortable: true,
                    },
                    {
                        data: 'operation_type.display_name',
                        sortable: true,
                    },
                    {
                        data: null,
                        name: 'hours',
                        sortable: true,
                        render: function(o) {
                            return (o.hours == null || o.hours <= 0) ? '--' : root.toFixedFmt(o.hours);
                        }
                    },
                    {
                        data: null,
                        name: 'ura_rate',
                        sortable: false,
                        render: function(o) {
                            return o.ura_rate == null ? '--' : root.asCurrency(o.ura_rate.amount);
                        }
                    },
                    {
                        data: null,
                        name: 'amount',
                        sortable: true,
                        className: 'ura-amt-td',
                        render: function(o) {
                            return root.asCurrency(o.amount);
                        },
                    },
                    {
                        name: 'balance',
                        data: null,
                        sortable: true,
                        render: function(o) {
                            return o.balance == null ? '--' : root.asCurrency(o.balance);
                        }
                    },
                    {
                        data: null,
                        name: 'notes',
                        render: function(o) {
                            if (o.notes == null) {
                                return '';
                            }
                            return `<a href="javascript:void(0)" class="note-pop"  data-placement="top" data-toggle="popover" data-trigger="focus" data-content="${o.notes}"><i class="fa fa-lg fa-info-circle" aria-hidden="true"></i></a>`;
                        },
                    },
                ],
            });
        },

    };

    //Document ready init
    $(function() {
        urat.init();
    });
</script>

@stop