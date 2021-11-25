@extends('adminlte::page')
@section('title', 'CPID')
@section('content_header')
<h1>CPID</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New CPID">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="cpid-table">
    <thead>
        <tr>
            <th>#</th>
            <th>CPID</th>
            <th>Short Name</th>
            <th>Positions</th>
            <th>Pay Standard</th>
            <th>Pay Overtime</th>
            <th>Pay Stat</th>
            <th>Bill Standard</th>
            <th>Bill Overtime</th>
            <th>Bill Stat</th>
            <th>Last Modified Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">CPID</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'cpid-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}

            <div class="modal-body">
                </ul>
                <div class="form-group row" id="cpid">
                    <label for="cpid" class="col-sm-3 control-label">CPID <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('cpid',null,array('class'=>'form-control', 'Placeholder'=>'CPID', "maxlength"=>"15")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="short_name">
                    <label for="short_name" class="col-sm-3 control-label">Short Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('short_name',null,array('class'=>'form-control', 'Placeholder'=>'Short Name')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="cpid_function_id">
                    <label for="cpid_function_id" class="col-sm-3 control-label">CPID Function</label>
                    <div class="col-sm-9">
                        {{Form::select('cpid_function_id',$cpidFunctions,null,['class' => 'form-control','placeholder' => 'Choose CPID function','id'=>'cpid_function','style'=>"width: 100%;"])}}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="position_id">
                    <label for="position_id" class="col-sm-3 control-label">Position <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{Form::select('position_id',$positions,null,['class' => 'form-control positions','placeholder' => 'Choose Position','id'=>'position_id','style'=>"width: 100%;"])}}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="description">
                    <label for="description" class="col-sm-3 control-label">Position Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description',null,array('class' => 'form-control', 'Placeholder'=>'Description','rows' => 3, 'cols' => 40)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="noc">
                    <label for="noc" class="col-sm-3 control-label">NOC</label>
                    <div class="col-sm-9">
                        {{ Form::number('noc',null,array('class'=>'form-control', 'Placeholder'=>'NOC',"step"=>"any")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="effective_from">
                    <label for="effective_from" class="col-sm-3 control-label">Effective From <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('effective_from',null,array('class'=>'form-control datepicker', 'Placeholder'=>'Effective Date From (Y-m-d)',"step"=>"any")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="p_standard">
                    <label for="p_standard" class="col-sm-3 control-label">Pay Standard <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('p_standard',null,array('class'=>'form-control', 'Placeholder'=>'$ 0.00',"step"=>"any")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="p_overtime">
                    <label for="p_overtime" class="col-sm-3 control-label">Pay Overtime <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('p_overtime',null,array('class'=>'form-control', 'Placeholder'=>'$ 0.00',"step"=>"any")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="p_holiday">
                    <label for="p_holiday" class="col-sm-3 control-label">Pay Stat <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('p_holiday',null,array('class'=>'form-control', 'Placeholder'=>'$ 0.00',"step"=>"any")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="b_standard">
                    <label for="b_standard" class="col-sm-3 control-label">Bill Standard <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('b_standard',null,array('class'=>'form-control', 'Placeholder'=>'$ 0.00',"step"=>"any")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="b_overtime">
                    <label for="b_overtime" class="col-sm-3 control-label">Bill Overtime <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('b_overtime',null,array('class'=>'form-control', 'Placeholder'=>'$ 0.00',"step"=>"any")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="b_holiday">
                    <label for="b_holiday" class="col-sm-3 control-label">Bill Stat <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::number('b_holiday',null,array('class'=>'form-control', 'Placeholder'=>'$ 0.00',"step"=>"any")) }}
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
<div class="modal fade" id="delete_Modal" tabindex="-1" role="dialog" aria-labelledby="delete_ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Warning</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action cannot be undone and you will be unable to recover any data.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>

            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<style type="text/css">
.view {
    padding-right: 8%;
}
</style>
<script>
    $(function() {

        $('select#position_id').select2({
            dropdownParent: $("#myModal"),
            placeholder: 'Choose Position'
        });

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#cpid-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function(e, dt, node, conf) {
                            emailContent(table, 'Cpid');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('cp-id.list') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // order: [
                //     [10, "desc"]
                // ],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false,
                    },
                    {
                        data: 'cpid',
                        name: 'cpid'
                    },
                    {
                        data: 'short_name',
                        name: 'short_name'
                    },
                    {
                        data: null,
                        render: function(o) {
                            if (o.position) {
                                return o.position.position
                            } else {
                                return '';
                            }
                        },
                        name: 'postion_name'

                    },
                    {
                        data: 'cpid_rates.p_standard',
                        name: 'cpid_rates.p_standard',
                    },
                    {
                        data: 'cpid_rates.p_overtime',
                        name: 'cpid_rates.p_overtime',
                    },
                    {
                        data: 'cpid_rates.p_holiday',
                        name: 'cpid_rates.p_holiday',
                    },
                    {
                        data: 'cpid_rates.b_standard',
                        name: 'cpid_rates.b_standard',
                    },
                    {
                        data: 'cpid_rates.b_overtime',
                        name: 'cpid_rates.b_overtime',
                    },
                    {
                        data: 'cpid_rates.b_holiday',
                        name: 'cpid_rates.b_holiday',
                    },

                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            var actions = '';
                            @can('edit_masters')
                            actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            @endcan
                            var url = '{{route("cp-id.history",'')}}';
                            actions += '<a href="' + url + "/" + o.id + '" title="View" class="view fa fa-eye"></a>'
                            @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                            @endcan
                            
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        /* Posting data to PositionLookupController - Start*/
        $('#cpid-form').submit(function(e) {
            e.preventDefault();
            if ($('#cpid-form input[name="id"]').val()) {
                var message = 'CPID has been updated successfully';
            } else {
                var message = 'CPID has been created successfully';
            }
            formSubmit($('#cpid-form'), "{{ route('cp-id.store') }}", table, e, message);
        });


        $("#cpid-table").on("click", ".edit", function(e) {
            var id = $(this).data('id');
            var url = '{{ route("cp-id.single",":id") }}';
            var url = url.replace(':id', id);
            $("#myModal .positions").prop('disabled', false);
            $('#cpid-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function(data) {
                    $("#cpid-form").trigger('reset');
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="cpid"]').val(data.cpid)
                        $('#myModal input[name="short_name"]').val(data.short_name)
                        $('#myModal input[name="pay_rate"]').val(data.pay_rate)
                        $('#myModal input[name="bill_rate"]').val(data.bill_rate)
                        $('#myModal textarea[name="description"]').val(data.description)
                        $('#myModal input[name="noc"]').val(data.noc)
                        $('#myModal select[name="position_id"] option[value="' + data.position_id + '"]').prop('selected', true)
                        $('#myModal select[name="cpid_function_id"] option[value="' + data.cpid_function_id + '"]').prop('selected', true)

                        if (data.position_id != 0) {
                            $("#myModal .positions").prop('disabled', true);
                        }

                        if (data.cpid_rates != null) {
                            $('#myModal input[name="effective_from"]').val(data.cpid_rates.effective_from)
                            $('#myModal input[name="p_standard"]').val(data.cpid_rates.p_standard)
                            $('#myModal input[name="p_overtime"]').val(data.cpid_rates.p_overtime)
                            $('#myModal input[name="p_holiday"]').val(data.cpid_rates.p_holiday)
                            $('#myModal input[name="b_standard"]').val(data.cpid_rates.b_standard)
                            $('#myModal input[name="b_overtime"]').val(data.cpid_rates.b_overtime)
                            $('#myModal input[name="b_holiday"]').val(data.cpid_rates.b_holiday)
                        }
                        $('#myModal select[name="position_id"]').trigger('change');
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit CPID: " + data.cpid)
                    } else {
                        alert(data);
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });

        $('#cpid-table').on('click', '.delete', function(e) {

            var id = $(this).data('id');
            var single_url = '{{ route("cp-id.check-cpid-allocation",":id") }}';
            var single_url = single_url.replace(':id', id);
            $.ajax({
                url: single_url,
                type: 'GET',
                data: "id=" + id,
                success: function(data) {
                    if (data > 0) {
                        swal({
                            title: "You won't be able to perform this action",
                            text: "Unallocate the CPID and try again",
                            type: "warning",
                            showCancelButton: false,
                            showLoaderOnConfirm: true,
                            showConfirmButton: true

                        });
                    } else {
                        var base_url = "{{ route('cp-id.destroy',':id') }}";
                        var url = base_url.replace(':id', id);
                        var message = 'CPID has been deleted successfully';
                        deleteRecord(url, table, message);
                    }
                }
            });
        });
        $('.add-new').click(function() {
            $(".positions").prop('disabled', false);
        });
    });
</script>
@stop
