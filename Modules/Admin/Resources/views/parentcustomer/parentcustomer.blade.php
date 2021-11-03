{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Parent Customer')

@section('content_header')
<h1>Parent Customer</h1>
@stop

@section('content')
<div id="message"></div>
@if(Session::has('customer-updated'))
    <div id="import-success-alert" class="alert alert-info fade in alert-dismissible" role="alert" style="width:50%;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
        {{ Session::get('customer-updated') }}
    </div>
@endif
<div class="add-new" onclick="addnew()" data-title="Add New Customer">Add <span class="add-new-label">New</span></div>
<fieldset>
    <div id="filter" style="display:none">
        <label><input type="radio" name="customer-contract-type" value="{{ PERMANENT_CUSTOMER }}" checked>&nbsp;Permanent</label>
        <label><input type="radio" name="customer-contract-type" value="{{ STC_CUSTOMER }}">&nbsp;Short Term Contracts</label>
    </div>
</fieldset>
<table class="table table-bordered" id="customer-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Project No</th>
            <th>Client Name</th>
            <th>City</th>
            <th>Client Contact Name</th>
            <th>Client Contact Email</th>
            <th>Client Contact Phone Number</th>

            <th>Actions</th>
        </tr>
    </thead>
</table>
{!! Form::open(array('route' => 'parentcustomer.import.process','method'=>'POST','files'=>'true')) !!}
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9" style="margin-top:30px;">
            <div class="col-md-12">
                {!! Form::label('import_file','Select File to Import:',['class'=>'col-md-4']) !!}
                    {!! Form::file('import_file', array('class' => 'col-md-4', 'accept'=>'.xls,.xlsx')) !!}

                    {!! Form::submit('Upload',['class'=>'btn btn-primary col-md-1']) !!}
            </div>
            {!! $errors->first('import_file', '<div class="col-md-12"><label class="col-md-10 align-center"><p class="help-block">:message</p></label></div>') !!}
            <div class="col-md-12">
                <a href="{{asset('excel_import_template/CGL360_Customer_Import_Template.xlsx')}}">Customer Import Template Format</a>
            </div>
        </div>
    </div>
{!! Form::close() !!}

<div id="myModal" class="modal fade" data-backdrop="static"  role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            {{ Form::open(array('url'=>'customer/store','id'=>'customer-form', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body" >
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <div class="form-group" id="project_number">
                    <label for="project_number" class="col-lg-4 control-label">@lang('Project Number <span class="mandatory">*</span>')</label>
                    {{ Form::text('project_number', null, array('class'=>'form-control project-number', 'placeholder'=>'Project Number')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="client_name">
                    <label for="client_name" class="col-lg-4 control-label">@lang('Client Name <span class="mandatory">*</span>')</label>
                    {{ Form::text('client_name', null, array('class'=>'form-control', 'placeholder'=>'Client Name')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_name">
                    <label for="contact_person_name" class="col-lg-4 control-label">@lang('Contact Person Name')</label>
                    {{ Form::text('contact_person_name', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Name')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_email_id">
                    <label for="contact_person_email_id" class="col-lg-4 control-label">@lang('Contact Person Email Id')</label>
                    {{ Form::email('contact_person_email_id', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Email Id')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_phone">
                    <label for="contact_person_phone" class="col-lg-4 control-label">@lang('Contact Person Phone')</label>
                    {{ Form::text('contact_person_phone', null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Phone [ format (XXX)XXX-XXXX ]')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_phone_ext">
                    <label for="contact_person_phone_ext" class="col-lg-4 control-label">@lang('Ext.')</label>
                    {{ Form::text('contact_person_phone_ext', null, array('class'=>'form-control', 'placeholder'=>'Ext.','maxlength'=>255)) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_cell_phone">
                    <label for="contact_person_cell_phone" class="col-lg-4 control-label">@lang('Contact Person Cell Phone')</label>
                    {{ Form::text('contact_person_cell_phone', null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Cell Phone [ format (XXX)XXX-XXXX ]')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_position">
                    <label for="contact_person_position" class="col-lg-4 control-label">@lang('Contact Person Position')</label>
                    {{ Form::text('contact_person_position', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Position')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="address">
                    <label for="address" class="col-lg-4 control-label">@lang('Address <span class="mandatory">*</span>')</label>
                    {{ Form::text('address', null, array('class'=>'form-control address_details', 'placeholder'=>'Address')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="city">
                    <label for="city" class="col-lg-4 control-label">@lang('City <span class="mandatory">*</span>')</label>
                    {{ Form::text('city', null, array('class'=>'form-control address_details', 'placeholder'=>'City')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="province">
                    <label for="Province" class="col-lg-4 control-label">@lang('Province <span class="mandatory">*</span>')</label>
                    {{ Form::text('province', null, array('class'=>'form-control address_details', 'placeholder'=>'Province')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="postal_code">
                    <label for="postal_code" class="col-lg-4 control-label">@lang('Postal Code <span class="mandatory">*</span>')</label>
                    {{ Form::text('postal_code', null, array('class'=>'postal-code form-control address_details','placeholder'=>'Postal Code')) }}
                    <small class="help-block"></small>
                </div>
                 <div class="form-group" id="billing_address">
                    <label for="billing_address" class="col-lg-3 control-label">@lang('Billing Address <span class="mandatory">*</span>')</label>
                     <label for="same_address_check" class="col-lg-3 control-label">@lang('Same as Site Address')</label>
                    {{ Form::checkbox('same_address_check',null,null, array('id'=>'check_same_address')) }}
                    {{ Form::text('billing_address', null, array('class'=>'form-control', 'placeholder'=>'Billing Address')) }}

                    <small class="help-block"></small>
                </div>


                <div class="form-group" id="industry_sector_lookup_id">
                    <label for="industry_sector_lookup_id" class="col-lg-4 control-label">@lang('Industry Sector<span class="mandatory">*</span>')</label>
                    {{ Form::select('industry_sector_lookup_id',[null=>'Select']+$lookups['industrySectorLookup'], old('industry_sector_lookup_id'),array('class' => 'form-control select2','style'=>"width: 100%;")) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="region_lookup_id">
                    <label for="region_lookup_id" class="col-lg-4 control-label">@lang('Region <span class="mandatory">*</span>')</label>
                    {{ Form::select('region_lookup_id',[null=>'Select']+$lookups['regionLookup'], old('region_lookup_id'),array('class' => 'form-control')) }}
                    <input class="region-description form-control" disabled />
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="description">
                    <label for="description" class="col-lg-4 control-label">@lang('Description')</label>
                    {{ Form::textArea('description', null, array('class'=>'form-control', 'placeholder'=>'Description','rows'=>5)) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="proj_open">
                    <label for="proj_open" class="col-lg-4 control-label">@lang('Project Open Date')</label>
                    {{ Form::text('proj_open', null, array('class'=>'form-control datepicker', 'placeholder'=>'Project Open Date (Y-m-d)')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="arpurchase_order_no">
                    <label for="arpurchase_order_no" class="col-lg-4 control-label">@lang('AR Purchase Order Number')</label>
                    {{ Form::text('arpurchase_order_no', null, array('class'=>'form-control', 'placeholder'=>'AR Purchase Order Number')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="arcust_type">
                    <label for="arcust_type" class="col-lg-4 control-label">@lang('AR Customer Type')</label>
                    {{ Form::text('arcust_type', null, array('class'=>'form-control', 'placeholder'=>'AR Customer Type')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="requester_name">
                    <label for="requester_name" class="col-lg-4 control-label">@lang('Requestor Name <span class="mandatory">*</span>')</label>
                    {{Form::select('requester_name',$lookups['requesterLookup'], old('requester_name'),['id'=>'requester_id', 'class' => 'form-control', 'placeholder' => 'Please Select','style'=>'width: 100%'])}}

                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="requester_position">
                    <label for="requester_position" class="col-lg-4 control-label">@lang('Requestor Position')</label>
                    {{ Form::text('requester_position', null, array('class'=>'form-control', 'placeholder'=>'Requestor Position','readonly'=>true,'disabled'=>true)) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="requester_empno">
                    <label for="requester_empno" class="col-lg-4 control-label">@lang('Requestor Employee Number')</label>
                    {{ Form::text('requester_empno', null, array('class'=>'form-control', 'placeholder'=>'Requestor Employee Number','readonly'=>true,'disabled'=>true)) }}
                    <small class="help-block"></small>
                </div>
                 <div class="form-group" id="show_in_sitedashboard">
                    <label for="show_in_sitedashboard" class="col-lg-4 control-label">@lang('Show in Site Dashboard')</label>
                    {{ Form::checkbox('show_in_sitedashboard', 1,'checked') }}
                    <small class="help-block"></small>
                </div>
                 <div class="form-group" id="shift_journal">
                    <label for="shift_journal" class="col-lg-4 control-label">@lang('Shift Journal')</label>
                    {{ Form::checkbox('shift_journal_enabled', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="time_shift_enabled" style="display:none">
                    <label for="time_shift_enabled" class="col-lg-4 control-label">@lang('Enable Time Shift')</label>
                    {{ Form::checkbox('time_shift_enabled', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="guard_tour">
                    <label for="guard_tour" class="col-lg-4 control-label">@lang('Guard Tour')</label>
                    {{ Form::checkbox('guard_tour_enabled', 1) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="interval_check" style="display:none">
                    <label for="interval_check" class="col-lg-4 control-label">@lang('Interval Check-in Required')</label>
                    {{ Form::checkbox('interval_check', 1) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="guard_tour_duration" style="display:none">
                    <div class="col-lg-4">
                    <label for="guard_tour_duration" class="control-label">@lang('Duration to set the interval (Hours)')</label></div>
                    <div class="col-lg-8">
                    {{ Form::text('guard_tour_duration', null, array('class'=>'form-control','placeholder'=>'Duration (Hours)', 'id' => 'duration')) }}
                    </div>
                    <div class="col-lg-12">
                    <small class="help-block"></small>
                    </div>
                </div>

                  <div class="form-group" id="overstay_enabled">
                    <label for="overstay_enabled" class="col-lg-4 control-label">@lang('Overtime Enabled')</label>
                    {{ Form::checkbox('overstay_enabled', 1) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="overstay_time" style="display:none">
                    <div class="col-lg-4">
                    <label for="overstay_time" class="control-label">@lang('Overstay Time')</label></div>
                    <div class="col-lg-8">
                    {{ Form::text('overstay_time', null, array('class'=>'form-control timepicki','placeholder'=>'Duration', 'id' => 'timepicker')) }}
                    </div>
                    <div class="col-lg-12">
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

<!-- Map Modal Start-->
<div id="mapModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modal-close" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Mark Location</h4>
            </div>
            <div class="modal-body">
                <div class="container">
                        <div class="row">
                    <div class="col-md-8">
                        <div id="MapContainer" style="height: 500px; "></div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Latitude</label>
                            <input type="text" id="lat" readonly/>
                        </div>
                        <div class="col-md-2">
                            <label>Longitude</label>
                            <input type="text" id="long" readonly/>
                        </div>
                        <div class="col-md-2 radius" style="display:none;">
                            <label>Radius</label>
                            <input type="number" id="radius"/><br/>
                            <input type="hidden" id="rowid" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="add-fence" style="display:none;">Add Fence</button>
                {{ Form::submit('Save', array('class'=>'button btn btn-primary','id'=>'latlong_submit'))}}
                <button class="btn btn-primary" id="modal_cancel" data-dismiss="modal" aria-hidden="true">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- Map Modal End-->


@stop


@section('js')
<script>


    $(function () {
         $('.select2').select2();
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#customer-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    },
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Customers');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":"{{ route('parentcustomer.list') }}",
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'project_number', name: 'project_number'},
                {data: 'client_name', name: 'client_name'},
                {data: 'city', name: 'city'},
                {data: 'contact_person_name', name: 'contact_person_name'},
                {data: 'contact_person_email_id', name: 'contact_person_email_id'},
                {data: 'contact_person_phone', name: 'contact_person_phone'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        actions += ''
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /*Filters for Permanent and STC customer - Start*/
        $('#filter').on('change', 'input[name=customer-contract-type]', function () {
            url = "{{route('parentcustomer.list',':type')}}";
            type = $('input[name=customer-contract-type]:checked').val();
            url = url.replace(':type', type);
            table.ajax.url(url).load();
        });
        /*Filters for Permanent and STC customer - End*/

        /* Customer Store - Start*/
        $('#customer-form').submit(function (e) {
            e.preventDefault();
            if($('#customer-form input[name="id"]').val()){
                var message = 'Customer has been updated successfully';
            }else{
                var message = 'Customer has been created successfully';
            }
            formSubmit($('#customer-form'), "{{ route('parentcustomer.store') }}", table, e, message);
        });
        /* Customer Store - End*/


        /* Customer Edit - Start*/
        $("#customer-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var base_url = "{{route('parentcustomer.single',':id')}}";
            var url = base_url.replace(':id',id);
            $('#customer-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        console.log(data)
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="project_number"]').val(data.project_number)
                        $('#myModal input[name="client_name"]').val(data.client_name)
                        $('#myModal input[name="contact_person_name"]').val(data.contact_person_name)
                        $('#myModal input[name="contact_person_email_id"]').val(data.contact_person_email_id)
                        $('#myModal input[name="contact_person_phone"]').val(data.contact_person_phone)
                        $('#myModal input[name="contact_person_phone_ext"]').val(data.contact_person_phone_ext)
                        $('#myModal input[name="contact_person_cell_phone"]').val(data.contact_person_cell_phone)
                        $('#myModal input[name="contact_person_position"]').val(data.contact_person_position)
                        $('#myModal select[name="requester_name"]').val(data.requester_name);

                        if(data.requester_position!=null && data.requester_empno!=null && isNaN(data.requester_name)){
                            //case when requestername is string
                        $("#myModal select[name='requester_name'] option").each(function()
                            {
                            var str=$(this).text() ;
                             if (str.indexOf(data.requester_empno) >= 0)
                             {

                                var val=$(this).val();
                                $('#myModal select[name="requester_name"]').val(val);
                                 $('#myModal input[name="requester_position"]').val(data.requester_position);
                                $('#myModal input[name="requester_empno"]').val(data.requester_empno);
                                 return false;
                             }
                             else
                             {
                        $('#myModal select[name="requester_name"]').val('');
                        $('#myModal input[name="requester_position"]').val('');
                        $('#myModal input[name="requester_empno"]').val('');
                             }
                            });
                        }
                        else if(data.requester_details!=null && data.requester_details.employee.employee_position!=null)
                        {
                        $('#myModal input[name="requester_position"]').val(data.requester_details.employee.employee_position.position);
                         $('#myModal input[name="requester_empno"]').val(data.requester_details.employee.employee_no);
                        }
                        else if(data.requester_details!=null && data.requester_details.employee.employee_position==null)
                        {
                        $('#myModal input[name="requester_position"]').val('');
                        $('#myModal input[name="requester_empno"]').val(data.requester_details.employee.employee_no);
                        }
                        else
                        {
                        $('#myModal input[name="requester_position"]').val('');
                        $('#myModal input[name="requester_empno"]').val('');
                        }
                        $('#myModal input[name="city"]').val(data.city)
                        $('#myModal input[name="postal_code"]').val(data.postal_code)
                        $('#myModal input[name="province"]').val(data.province)
                        $('#myModal input[name="address"]').val(data.address)
                        $('#myModal textarea[name="description"]').val(data.description);
                        $('#myModal input[name="proj_open"]').val(data.proj_open);
                        $('#myModal input[name="arpurchase_order_no"]').val(data.arpurchase_order_no);
                        $('#myModal input[name="arcust_type"]').val(data.arcust_type);
                        $('#myModal select[name="industry_sector_lookup_id"]').val(data.industry_sector_lookup_id);
                        $('#myModal select[name="region_lookup_id"]').val(data.region_lookup_id);
                        $('select[name="region_lookup_id"]').trigger('change');
                         $('#myModal input[name="billing_address"]').val(data.billing_address);
                           $('#myModal input[name="same_address_check"]').prop( "checked", false );
                         var full_address=data.address+', '+data.city+', '+data.province+', '+data.postal_code;

                         if(data.billing_address!=null){
                         if(full_address.trim()===data.billing_address.trim())
                         {
                            $('#myModal input[name="same_address_check"]').prop( "checked", true );
                         }
                         }
                         if(data.show_in_sitedashboard)
                         {
                           $('#myModal input[name="show_in_sitedashboard"]').prop( "checked", true );
                         }
                         else
                         {
                             $('#myModal input[name="show_in_sitedashboard"]').prop( "checked", false );
                         }
                        if(data.guard_tour_enabled){
                            $('#myModal input[name="guard_tour_enabled"]').prop( "checked", true );

                        }else{
                            $('#myModal input[name="guard_tour_enabled"]').prop( "checked", false );
                        }
                        if(data.guard_tour_duration)
                        {
                            $('#myModal input[name="interval_check"]').prop( "checked", true );
                            $('#interval_check').show();
                            $('#guard_tour_duration').show();
                            $('#myModal input[name="guard_tour_duration"]').val(data.guard_tour_duration);
                        }else{
                            $('#myModal input[name="interval_check"]').prop( "checked", false );
                            $('#interval_check').hide();
                            $('#guard_tour_duration').hide();
                        }
                         if(data.overstay_enabled)
                         {
                           $('#myModal input[name="overstay_enabled"]').prop("checked", true );
                            $('#overstay_time').show();
                            $('#myModal input[name="overstay_time"]').val(data.overstay_time);
                         }
                         else
                         {
                          $('#myModal input[name="overstay_enabled"]').prop( "checked", false );
                            $('#overstay_time').hide();
                            $('#myModal input[name="overstay_time"]').val(data.overstay_time);
                         }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Customer:  " + data.client_name)
                        $("#requester_id").select2();
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
        /* Customer Edit - End*/

        /* Get region data */
        $('select[name="region_lookup_id"]').on('change',function(){
            var id = $(this).val();
            $('.region-description').val("");
            if($.isNumeric(id))
            {
                var base_url = "{{route('region.single',':id')}}";
                var url = base_url.replace(':id',id);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('.region-description').val(data.region_description);
                    }
                });
            }
        });
        /* Get region data */

        /* Customer Delete - Start*/
        $('#customer-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{route('parentcustomer.destroy',':id')}}"
            var url = base_url.replace(':id',id);
            var message = 'Customer has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Customer Delete - End*/

        //To reset the hidden value in the form
        $('#myModal').on('hidden.bs.modal', function () {
            $('#customer-form').find('input[name="id"]').val('0');
            $('#interval_check').hide();
            $('#guard_tour_duration').hide();
            $('input:text[name="billing_address"]').prop('readonly', false);
        });



    /*Hide alert -start*/
    /*$("#import-success-alert").fadeTo(2000, 500).slideUp(500, function(){
    $("#import-success-alert").slideUp(500);
    });*/
    /*Hide alert -end*/

    /* Show/Hide fields - Start */
    $('#shift_journal').find('input').change(function() {
        if($(this).is(":checked")) {
            $('#time_shift_enabled').show();
        }else{
            $('#time_shift_enabled').hide();
            $('#time_shift_enabled').find('input').prop('checked',false);
        }
    });
    $('#guard_tour').find('input').change(function() {
        if($(this).is(":checked")) {
            $('#interval_check').show();
        }else{
            $('#interval_check').hide();
            $('#guard_tour_duration').hide();
            $('#interval_check').find('input').prop('checked',false);
            $('#duration').val('');
        }
    });
    $('#interval_check').find('input').change(function() {
        if($(this).is(":checked")) {
            $('#guard_tour_duration').show();
        }else{
            $('#guard_tour_duration').hide();
            $('#guard_tour_duration').find('input').prop('checked',false);
            $('#duration').val('');
        }
    });
     $('#overstay_enabled').find('input').change(function() {
        if($(this).is(":checked")) {
            $('#overstay_time').show();

        }else{
            $('#overstay_time').hide();
             $('#timepicker').val('');

        }
    });
    /* Show/Hide fields - End */

    /*Clear MapContainer previous data - Start*/
        $("#modal_cancel").click(function () {
            $("#MapContainer").html("");
            $("#lat").val('');
            $("#long").val('');
            $("#radius").val('');
        });

        $("#modal-close").click(function () {
            $("#MapContainer").html("");
            $("#lat").val('');
            $("#long").val('');
            $("#radius").val('');
        });
    /*Clear MapContainer previous data - End*/


    /* Map Location click&Submit - Start */

        $('#latlong_submit').on('click', function (e) {
            id = $("#rowid").val();
            lat = $("#lat").val();
            long = $("#long").val();
            radius = $("#radius").val();
            if (radius != 0 && radius < 150) {
                swal("", "The radius should be either 0 or greater than or equal to 150mtrs", "warning");
            } else {
                $.ajax({
                    url: "{{route('parentcustomer.updateLatLong')}}",
                    type: 'POST',
                    data: {
                        'id': id,
                        'lat': lat,
                        'long': long,
                        'radius': radius
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (data.success) {
                            swal("Success", "The geo location details updated successfully", "success");
                            //$('#message').html(data.payload);
                            $('#mapModal').modal('hide');
                            $("#MapContainer").html("");
                            table.ajax.reload();
                        } else {
                            //alert(data);
                            console.log(data);
                            swal("Oops", "The geo location details updation was unsuccessful", "warning");
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            }
        });



        /* Function for add/edit fence - Start */
        function initialize(myCenter, radius) {
            var renderContainer = document.getElementById("MapContainer");
            var mapProp = {center: myCenter, zoom: 8};
            var map = new google.maps.Map(renderContainer, mapProp,{
                gestureHandling  : 'greedy',
            });

            //Marker in the Map
            var marker = new google.maps.Marker({
                position: myCenter,
                draggable: true,
                //animation: google.maps.Animation.DROP,
            });
            marker.setMap(map);

            //Circle in the Map
            var circle = new google.maps.Circle({
                center: myCenter,
                map: map,
                radius: radius, // IN METERS.
                fillColor: '#FF6600',
                fillOpacity: 0.3,
                strokeColor: "#FFF",
                strokeWeight: 1,
                //draggable: true,
                editable: true
            });
            circle.setMap(map);

            //Add listner to change latlong value on dragging the marker
            marker.addListener('dragend', function (event)
            {
                $('#lat').val(event.latLng.lat());
                $('#long').val(event.latLng.lng());
            });

            //Add event listner on drag event of marker
            marker.addListener('drag', function (event) {
                circle.setOptions({center: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
            });

            //Add listner to change radius value on field
            circle.addListener('radius_changed', function () {
                $('#radius').val(circle.getRadius());
            });

            //Add event listner on drag event of circle
            circle.addListener('drag', function (event) {
                marker.setOptions({position: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
            });

            //changing the radius of circle on changing the numeric field value
            $("#radius").on("change paste keyup keydown", function () {
                //radius = $("#radius").val();
                circle.setRadius(Number($("#radius").val()));

            });
        }
        /* Function for add/edit fence - End */

        $('#add-fence').on('click', function (e) {
            $('#add-fence').hide();
            $('.radius').show();
        });

         /* Function for concatinating address and populate in billing address if checkbox is checked - Start */
        $("#check_same_address").click(function() {
        if($("input[name=address]").val().length<=0||$("input[name=city]").val().length<=0||$("input[name=province]").val().length<=0||$("input[name=postal_code]").val().length<=0)
        {
              swal("Warning", "Please enter address details", "warning");
              $(this).prop('checked', false);
        }
        if (this.checked) {
            var address='';
            var city='';
            var province='';
            var postal_code='';
        if($("input[name=address]").val().length>0)
         var address=  $("input[name=address]").val()+', ';
        if($("input[name=city]").val().length>0)
         var city=  $("input[name=city]").val()+', ';
        if($("input[name=province]").val().length>0)
         var province=  $("input[name=province]").val()+', ';
         var postal_code=  $("input[name=postal_code]").val();
         var full_addr=address+city+province+postal_code;
         $('input:text[name="billing_address"]').val(full_addr);
         $('input:text[name="billing_address"]').prop('readonly', true);
        }
        else
        {
            $('input:text[name="billing_address"]').val('');
            $('input:text[name="billing_address"]').prop('readonly', false);
        }
    });
        /* Function for concatinating address and populate in billing address if checkbox is checked - End */

        $('#customer-table').on('click', '.map_location', function (e) {
            $('#mapModal').off('shown.bs.modal');
            id = $(this).data('id');
            postal_code = $(this).data('postal_code');
            url = 'https://maps.google.com/maps/api/geocode/json?address='+postal_code+'&sensor=false&key={{config('globals.google_api_key')}}';
            if( ($(this).data('lat') == '' || $(this).data('lat') == null) || ($(this).data('long') == '' || $(this).data('long') == null) )
            {
                $.ajax({
                    url: url,
                    type: 'GET',
                    crossDomain: true,
                    dataType: 'json',
                    success: function (data) {
                        if (data.results.length > 0) {
                            lat = data.results[0].geometry.location.lat;
                            long = data.results[0].geometry.location.lng;
                            $('#lat').val(lat);
                            $('#long').val(long);
                        } else {
                            lat = {{config('globals.map_default_center_lat')}};
                            long = {{config('globals.map_default_center_lng')}};
                            $('#lat').val(lat);
                            $('#long').val(long);
                        }
                        setFence($(this));
                    },
                    error: function (xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                    },
                });
            }
            else{
                lat = $(this).data('lat');
                long = $(this).data('long');
                $('#lat').val(lat);
                $('#long').val(long);
                setFence($(this));
            }

            function setFence(curr_obj){
                radius = Number((curr_obj.data('radius') != '' && curr_obj.data('radius') != null) ? curr_obj.data('radius') : 0);
                $('#rowid').val(id);
                $('#radius').val(radius);
                $('.radius').show();
                if (radius == 0) {
                    $('.radius').hide();
                    $('#add-fence').show();
                }
                $('#mapModal').modal();
                $('#mapModal').on('shown.bs.modal', function (e) {
                    initialize(new google.maps.LatLng(lat, long), radius);
                });
            }
        });

             /* Prepopulating employee details on choosing select2 - Start */
             $('#requester_id').on('change', function () {
                if($(this).val()=='')
                {
                $('input:text[name="requester_position"]').val('');
                $('input:text[name="requester_empno"]').val('');
                }
                var url = '{{ route("user.formattedUserDetails", ["id" => ":user_id"]) }}';
                    url = url.replace(':user_id', $(this).val());
                    $.ajax({
                        url:url,
                        method: 'GET',
                        success: function (data) {
                           $('input:text[name="requester_position"]').val(data.position).prop('readonly','true');
                           $('input:text[name="requester_empno"]').val(data.employee_no).prop('readonly','true');
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                    });

            });
             /* Prepopulating employee details on choosing select2 - End */


    /* Map Location click&Submit - End */

    });

    $( function() {
    $( ".edittask" ).tooltip();
  } );
 $(document).keyup(function(e) {jQuery
         if (e.key === "Escape") {
          $("#myModal").modal('hide');
       }
   });

function addnew() {
        $("#myModal").modal();
        $("#requester_id").select2();
        $("#requester_id").val('').trigger('change') ;
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script src="{{ asset('js/timepicki.js') }}"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
 <link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
@stop
