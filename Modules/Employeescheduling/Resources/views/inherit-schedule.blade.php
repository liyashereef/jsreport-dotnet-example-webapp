@extends('layouts.app') @section('content')
@section('content')
<style>

</style>

<div class="table_title" style="margin-bottom: 35px;">
    <h4>Inherit Schedule</h4>
</div>

<div class="col-md-12" style="margin-top: 200px;">
    <div class="row">
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4">
            <select id="source_customer_element">
            </select>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
    <div class="row">&nbsp;</div>

    <div class="row">
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4">
            <select id="source_pay_period_element">
            </select>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
    <div class="row">&nbsp;</div>

    <div class="row">
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4">
            <select id="destination_pay_period_element">
            </select>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
    <div class="row">&nbsp;</div>

    <div class="row">
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-3">
            <input type="button" class="btn btn-sm btn-primary inherit_allocations" value="Inherit Allocations" />
            </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
    </div>
</div>
@stop

@section('scripts')
<script>
        $(function(){
            $("#source_pay_period_element").select2({
                placeholder: "Choose any Source Pay Period"
            });

            $("#destination_pay_period_element").select2({
                multiple:true,
                placeholder: "   Destination Pay Period's Here"
            });

            $("#source_customer_element").select2({
                placeholder: "Choose any customer"
            });

            fnLoadCustomers();
        });


        function fnLoadCustomers() {
            $.ajax({
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('inherit-schedule.customers')}}",
                success: function (response) {
                    if(response.success) {
                        let options = '<option value="">Choose any Customer</option>';
                        $.each(response.data, function( key, value ) {
                            options +=`<option value="${key}">${value}</option>`;
                        });
                        $('#source_customer_element').html(options);
                    }
                }
            });
        }

        $('#source_customer_element').on('change', function() {
            $('#source_pay_period_element').html('<option value="">Choose any Payperiod</option>');
            $('#destination_pay_period_element').html('<option value="">Choose any Payperiod</option>');
            $.ajax({
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('inherit-schedule.source-payperiod')}}",
                data: {'customer_id':$(this).val()},
                success: function (response) {
                    if(response.success) {
                        let options = '<option value="">Choose any Payperiod</option>';
                        $.each(response.data, function( key, value ) {
                            options +=`<option value="${value['id']}">${value['pay_period_name']}&nbsp;(${value['short_name']})</option>`;
                        });
                        $('#source_pay_period_element').html(options);
                    }
                }
            });
        });

        $('#source_pay_period_element').on('change', function() {
            $('#destination_pay_period_element').html('<option value="">Choose any Payperiod</option>');
            let source_payperiod = $('#source_pay_period_element').val();

            $.ajax({
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('inherit-schedule.destination-payperiod')}}",
                data: {'source_payperiod':source_payperiod},
                success: function (response) {
                    if(response.success) {
                        let options = '<option value="">Choose any Payperiod</option>';
                        $.each(response.data, function( key, value ) {
                            if(source_payperiod != value['id']) {
                                options +=`<option value="${value['id']}">${value['pay_period_name']}&nbsp;(${value['short_name']})</option>`;
                            }
                        });
                        $('#destination_pay_period_element').html(options);
                    }
                }
            });
        });

        $('.inherit_allocations').on('click', function(){
            let customer_id = $('#source_customer_element').val();
            let source_payperiod = $('#source_pay_period_element').val();
            let destination_payperiod = $('#destination_pay_period_element').val();

            if(customer_id == "") {
                swal('Error','Please choose any customer', 'error');
                return false;
            }else if(source_payperiod == "") {
                swal('Error','Please choose any source payperiod', 'error');
                return false;
            }else if(destination_payperiod == "") {
                swal('Error',"Please choose destination payperiod", 'error');
                return false;
            }else{
                swal({
                        title: "Are you sure?",
                        text: "Inherit selected payperiod's. Proceed?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-warning",
                        confirmButtonText: "Yes",
                        showLoaderOnConfirm: true,
                        closeOnConfirm: false
                    },
                    function () {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url:"{{route('inherit-schedule.process')}}",
                            type:'post',
                            data:{
                                "customer_id":customer_id,
                                "source_payperiod":source_payperiod,
                                "destination_payperiod":destination_payperiod
                            },
                            success: function (resp) {
                                if(resp.success) {
                                    fnClearContent();
                                    swal("Success","Schedule inherited successfully", "success");
                                }else{
                                    swal("Error",resp.msg, "error");
                                }
                            },
                            error: function (xhr, textStatus, thrownError) {
                            },
                        });
                });
            }
        });

        function fnClearContent() {
            $('#source_customer_element').val('').trigger('change');
            $('#source_pay_period_element').val('').trigger('change');
            $('#destination_pay_period_element').val('').trigger('change');
        }
</script>

@stop
