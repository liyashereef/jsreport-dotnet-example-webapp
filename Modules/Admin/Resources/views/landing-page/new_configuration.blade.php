

{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Landing Page Widget Configurations')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3 class="orange">Landing Page Widget Configurations</h3>
@stop

@section('content')
<!-- START-- Landing Page Tab -->
<style>
    .astrick-symbol {
        color: red;
        vertical-align: top;
        float: right;
        font-weight: bold;
        margin-top: -3px;
    }

    .required-input {
        width: 93%;
        float: left;
    }
</style>


<div role="landingPage" class="tab-pane" id="landingPage" style="overflow-y: auto;">
    <div class="container-fluid">
        <div class="row">
            <!-- Tab Name -->
            <div class="form-group">
                <div class="col-md-1 col-sm-1 col-lg-1">
                    <label for="tabName">Tab Name</label>
                    <span class="mandatory" style="font-weight: bold;">*</span>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <input type="hidden" class="form-control" name="tabId"  id="tabId" value="@if ((isset($tabDetails) && !empty($tabDetails))){{$tabDetails['id']}}@endif">
                    <input type="text" class="form-control alphanumeric_only" title="Tab name to display in dashabord" name="tabName" maxlength="40"  id="tabName"  value="@if ((isset($tabDetails) && !empty($tabDetails))){{$tabDetails['tab_name']}}@endif">
                    <small class="help-block"></small>
                </div>

                <div class="col-md-1 col-sm-1 col-lg-1 text-right">
                    <label for="tabName">Order</label>
                    <span class="mandatory" style="font-weight: bold;">*</span>
                </div>
                <div class="col-md-1 col-sm-1 col-lg-1" style="padding-left: 0px;">
                    <input type="number" class="form-control" title="Order to display in dashbaord" placeholder="Order" name="seq_no"  id="seq_no" min="1" value="@if ((isset($tabDetails) && !empty($tabDetails))){{$tabDetails['seq_no']}}@endif">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group" style="padding-top: 1%;">
                <div class="col-md-1 col-sm-1 col-lg-1">
                    <label for="configuration">Configuration</label>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <div style="border: 1px solid black;padding: 3%;" title="Layout display in dashboard">
                        <table class="table-borderless">
                            <tbody>
                                @foreach ($widgetLayouts as $key=>$value)
                                <tr>
                                    <td>
                                        <input type="radio" name="layout" id="layout_{{ $value->id }}" value="{{ $value->id }}" onclick="loadWidgetGrid('{{$value->id}}')" @if((isset($tabDetails) && !empty($tabDetails) && ($tabDetails['landing_page_widget_layout_id'] === $value->id))) {{"checked='checked'"}} @endif>
                                        <label for="{{ $value->name }}">{{ $value->name }}</label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

         <div class="row second-section">
            <div class="form-group" style="padding-top: 1%;">
                <div class="col-md-1 col-sm-1 col-lg-1">&nbsp;</div>
                <div class="col-md-11 col-sm-11 col-lg-11">
                    @if((isset($tabDetails) && !empty($tabDetails) && ($tabDetails['default_tab_structure'] === 1)))
                        <input type="checkbox" class="form-control-sm" name="default_tab_structure"  id="default_tab_structure" checked/>
                    @else
                        <input type="checkbox" class="form-control-sm" name="default_tab_structure"  id="default_tab_structure"/>
                    @endif
                    <label for="default_tab_structure">Set as default tab structure</label>
                </div>
            </div>
        </div>

        <div class="row second-section"  style="padding-top: 1%;">
            <div class="form-group" id="landing_page_configuration_form">
            </div>
        </div>

        <!-- save button -->
        <div class="form-group row second-section">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <input type="hidden" id="customer_id" name="customer_id" value="{{$customerId}}">
                <div class="add-new" id="save_tab_configurations" onclick="save_tab_configurations();" style="display: none;">Save Configurations</div>
            </div>
        </div>
    </div>
</div>
<!-- END-- Landing Page Tab -->

@stop


@section('js')
<script type="text/javascript">
    $(function(){
        $('.second-section').hide();
        var layoutId = $('input[name=layout]:checked').val();
        if(layoutId !== "" && layoutId !== undefined) {
            $('.second-section').show();
        }
        //Declaring localStorage as null
        localStorage.setItem('final-drop-list', null);
        $(".required-input").after("<span class='astrick-symbol'>*</span>");;

        @if(!empty($tabDetails) && !empty($tabDetails['landing_page_widget_layout_id']))
            loadWidgetGrid('{{$tabDetails['landing_page_widget_layout_id']}}');
            $('#save_tab_configurations').show();
        @endif
    });
    /**On changes of `Set as default tab structure`
     * Set and remove elements.
     * Adding selected modules in localStorage.

     */
    $('#default_tab_structure').on('change', function(){
        $('#fields_div').html('');
        $('#save_tab_configurations').hide();
        $('#save_fields').hide();

        var default_structure = $(this).prop("checked");
        $('.module-draggable').html('');
        $('.module-droppable').html('');
        var customer_id = $('#customer_id').val();
        var tab_id = $('#tabId').val();

        $.ajax({
            type: "GET",
            url: "{{route('landing_page.getAllWidgetModules')}}",
            data: {
                'customer_id': customer_id,
                'tab_id': tab_id,
                'default_structure':default_structure
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.modules !== undefined && (response.modules != "")) {
                    var module_arr = Object.values(response.modules);
                    module_arr.forEach(function (module) {
                        $('#module_field_array_'+module.id+'_'+module.model).remove();
                        $('.module-draggable').append('<li data-draggable="item" class="li-droppable" data-widget-name="'+module.name+'" data-model="'+module.model+'" data-customer-widget="'+module.api_type+'" data-id="'+module.id+'" onclick="load_field_details(event, \''+module.id+'\', \''+module.model+'\')" draggable="true" aria-grabbed="false" tabindex="0">'+module.name+'&nbsp;&nbsp;<span class="close-li" onclick="deleteItemSelected(event);">x</span></li>');
                    });
                    $("#module-list-ol li").sort(sort_li).appendTo('#module-list-ol');
                }

                if(response.dropped_modules !== undefined && (response.dropped_modules != "")) {
                    var dropped_modules_arr = response.dropped_modules;
                    for (var layoutKey in dropped_modules_arr) {
                        let model = dropped_modules_arr[layoutKey]['model'];
                        let moduleId = dropped_modules_arr[layoutKey]['id'];
                        let moduleName = dropped_modules_arr[layoutKey]['name'];
                        let apiType = dropped_modules_arr[layoutKey]['api_type'];
                        $('#module_field_array_'+moduleId+'_'+model).remove();

                        if(default_structure && (apiType == 2 || apiType == 0)) {
                            $('ol[data-id="'+layoutKey+'"]').append('<li data-draggable="item" class="li-droppable" data-widget-name="'+moduleName+'" data-model="'+model+'" data-customer-widget="'+apiType+'" data-id="'+moduleId+'" onclick="load_field_details(event, \''+moduleId+'\', \''+model+'\')" draggable="true" aria-grabbed="false" tabindex="0">'+moduleName+'&nbsp;&nbsp;<span class="close-li" onclick="deleteItemSelected(event);">x</span></li>');
                        }else if((!default_structure) && (apiType == 1 || apiType == 0)) {
                            $('ol[data-id="'+layoutKey+'"]').append('<li data-draggable="item" class="li-droppable" data-widget-name="'+moduleName+'" data-model="'+model+'" data-customer-widget="'+apiType+'" data-id="'+moduleId+'" onclick="load_field_details(event, \''+moduleId+'\', \''+model+'\')" draggable="true" aria-grabbed="false" tabindex="0">'+moduleName+'&nbsp;&nbsp;<span class="close-li" onclick="deleteItemSelected(event);">x</span></li>');
                        }
                        $('#module_field_array_'+moduleId+'_'+model).remove();
                    }
                }

                $('.module-droppable .li-droppable').each(function() {
                    $(this).trigger('click');
                });

                var total_drop_box_count = document.querySelectorAll('#realImageDiv .module-droppable').length;
                var total_drop_box_li_count = document.querySelectorAll('.module-droppable .li-droppable').length;
                var module_fields_mapping_count = document.querySelectorAll('#hidden_array_section .module_hidden_input').length;
                console.log(total_drop_box_count + ', ' + total_drop_box_li_count + ', '+ module_fields_mapping_count);
                if((total_drop_box_count == total_drop_box_li_count) && (total_drop_box_count == module_fields_mapping_count)){
                    $('#save_tab_configurations').show();
                }else {
                    $('#save_fields').show();
                }
            }
        });
    });

    function loadWidgetGrid(widget_layout_id) {
        var customer_id = $('#customer_id').val();
        var tab_id = $('#tabId').val();
        var default_tab_structure = $('#default_tab_structure').prop('checked');

        $('.second-section').hide();
        var layoutId = $('input[name=layout]:checked').val();
        if(layoutId !== "" && layoutId !== undefined) {
            $('.second-section').show();
        }

        $('#landing_page_configuration_form').html('')
        $.ajax({
            type: "POST",
            url: "{{route('landing_page.getWidgetLayoutDetails')}}",
            data: {
                'widget_layout_id': widget_layout_id,
                'customer_id': customer_id,
                'default_tab_structure':  (default_tab_structure)? 1 :0,
                'tab_id': tab_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.html !== "") {
                    $('#landing_page_configuration_form').html(response.html);
                    $('#save_fields').show();
                }
            }
        });
    }

    function save_tab_configurations() {
        var default_tab_structure = $('#default_tab_structure').prop('checked');
        var seq_no = $('#seq_no').val();
        var customer_id = $('#customer_id').val();
        var tabName = $('#tabName').val();
        var layoutId = $('input[name=layout]:checked').val();
        var tab_id = $('#tabId').val();

        if(tabName === "") {
            $('#tabName').focus();
            swal('Oops', 'Tab name cannot be empty', 'error');
            return false;
        }else if(seq_no === "" || seq_no == 0) {
            $('#seq_no').focus();
            swal('Oops', 'Tab order cannot be empty', 'error');
            return false;
        }else if(layoutId === "" || layoutId === 'undefined' || layoutId === undefined) {
            swal('Oops', 'Please choose any configuration', 'error');
            return false;
        }

        var total_drop_box_li_count = document.querySelectorAll('.module-droppable .li-droppable').length;
        var total_drop_box_count = document.querySelectorAll('#realImageDiv .module-droppable').length;
        if (total_drop_box_count !== total_drop_box_li_count) {
            swal('Oops', (total_drop_box_count - total_drop_box_li_count) + ' more box to drop');
            return false;
        }

        var module_fields_mapping_count = document.querySelectorAll('#hidden_array_section .module_hidden_input').length;
        if (total_drop_box_count != module_fields_mapping_count) {
            swal('Oops', 'Pending fields mapping for some more modules(' + (total_drop_box_count - module_fields_mapping_count) + '/' + total_drop_box_count + ')');
            return false;
        }

        var result_array = [];
        $('.module_hidden_input').each(function() {
            if ($(this).val() !== "") {
                var array_values = JSON.parse($(this).val());
                result_array.push(array_values);
            }
        });
        if (result_array.length === 0) {
            swal('Oops', 'Something went wrong', 'error');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{route('landing_page.saveTabDetails')}}",
            data: {
                'tab_id': tab_id,
                'result_array': result_array,
                'customer_id': customer_id,
                'tabName': tabName,
                'layoutId': layoutId,
                'seq_no': seq_no,
                'default_tab_structure': (default_tab_structure)? 1 :0
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.status === "success") {
                    if(tab_id !== "") {
                        swal(response.status_msg, response.msg, response.status);
                        setTimeout(function(){
                            window.close();
                        }, 2000);
                    }else {
                        swal({
                            title: "Success",
                            text: response.msg,
                            type: response.status,
                            confirmButtonText: "OK"
                        },
                        function () {
                            let url = "{{ route('landing_page.new_configuration_window',['customer_id' => ''])}}" + customer_id + '';
                            window.location = url;
                        });
                    }
                }else {
                    swal(response.status_msg, response.msg, response.status);
                }
            }
        });
    }

    $('#seq_no').on('keyup', function(){
        var seq_no = $(this).val();
        var seq_no = seq_no.replace('.', '',-1);
        var seq_no = seq_no.replace('-', '',-1);
        $(this).val(seq_no);
    });
</script>
@stop
