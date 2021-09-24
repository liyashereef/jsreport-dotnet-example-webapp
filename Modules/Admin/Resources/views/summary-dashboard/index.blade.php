@extends('adminlte::page')

@section('title', 'Summary Dashboard Configurations')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h1>Summary Dashboard Configurations</h1>
@stop
@section('content')
<form name="summary_dashboard_form" id="summary_dashboard_form" method="post">
    <div class="col-md-8">

    <div class="row" style="padding-bottom: 5px">
        <div class="form-group"  >
            <label for="module" class=" col-md-3" style="padding-left: 0px">Dashboard Employee Survey<span class="mandatory"></span></label>
            <div class="col-md-4" style="padding-left: 5px">
                <select id="default_employeesurvey" name="default_employeesurvey" class="form-control" >
                    <option value="">Select Any</option>
                    @foreach ($employeeSurveys as $employeeSurvey)
                            <option value="{{$employeeSurvey->id}}">{{$employeeSurvey->survey_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4" style="padding-left: 5px">
                <button type="button" class="btn  btn-primary  form-control setdefault">Set Default Employee Survey</button>
            </div>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="summary-dashboard-from col-md-8">
            <div class="row">
                <div class="form-group"  id="module">
                    <label for="module" class="col-form-label col-md-3">Module<span class="mandatory">*</span></label>
                    <div class="col-md-4">
                        <select id="type" name="type" class="form-control" required>
                            <option value="">-- select --</option>
                            <option value="1">Guard Tour Compliance</option>
                            <option value="2">Site Turn Over</option>
                            <option value="3">Training Compliance</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">&nbsp;</div>
                <div id="dynamic_form_area">
                </div>
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-sm btn-primary text-right save-config">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('js')
<script>
    var sl_value = 0;

    $(function(){
        $('.save-config').hide();
    });

    function addItem() {
        $('#dynamic_form_area').append('<div class="item_list item_list_'+sl_value+'"><div class="row">&nbsp;</div><div class="row"><div class="form-group item_list" id="value"> <label for="value_'+sl_value+'" class="col-form-label col-md-2"></label> <div class="col-md-2"> <input type="text" id="value_'+sl_value+'" name="value[]" class="form-control" required/> </div> <div class="col-md-2"> <select id="color_'+sl_value+'" name="color[]" class="form-control" required><option value="red">Red</option><option value="yellow">Yellow</option><option value="green">Green</option><option value="black">Black</option></select> </div><div class="col-md-1" style="padding-top: 8px;"> <i class="fa fa-minus" aria-hidden="true" style="cursor:pointer;" onclick="removeItem('+sl_value+')"></i> </div></div></div></div>');
        sl_value++;
    }

    function removeItem(sl_no) {
        sl_value--;
        $('.item_list_'+sl_no).remove();
    }

    $('#summary_dashboard_form').submit(function(e){
        e.preventDefault();
        let $form = $('#summary_dashboard_form');
        let formData = $(this).serializeArray();
        $.ajax({
            url: "{{route('admin.summary-dashboard-configuration.store')}}",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            success: function(response) {
                if(response.success) {
                    swal('Success', 'Operation successfully completed', 'success');
                }else{
                    swal('Error', response.message, 'error');
                }
            }
        });
    });

    $('#type').on('change', function() {
        $('.save-config').hide();
        $('#dynamic_form_area').html('');
        let type = $(this).val();

        if(type == "") {
            $('#dynamic_form_area').html("");
        }else {
            $.ajax({
                url: "{{route('admin.summary-dashboard-configuration.list')}}",
                data: {'type':type},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                success: function(response) {
                    if(response.success) {
                        let html = '';
                        let len = Object.keys(response.data).length;
                        if(len > 0) {
                            $(response.data).each(function(ind, itemVal){
                                if(ind == 0) {
                                    sl_value=1;
                                    html +='<div class="item_list item_list_'+ind+'"><div class="row">&nbsp;</div><div class="row"><div class="form-group item_list" id="value"> <label for="value_'+ind+'" class="col-form-label col-md-3">Criticality Level(s)<span class="mandatory">*</span></label> <div class="col-md-2"> <input type="text" id="value_'+ind+'" name="value[]" value="'+itemVal.value+'" class="form-control" required/> </div> <div class="col-md-2"> <select id="color_'+ind+'" name="color[]" class="form-control" required><option value="red" '+((itemVal.color == 'red')? "selected": "")+'>Red</option><option value="yellow" '+((itemVal.color == 'yellow')? "selected": "")+'>Yellow</option><option value="green" '+((itemVal.color == 'green')? "selected": "")+'>Green</option><option value="black" '+((itemVal.color == 'black')? "selected": "")+'>Black</option></select> </div><div class="col-md-1" style="padding-top: 8px;">  <i class="fa fa-plus" onclick="addItem()" aria-hidden="true" style="cursor:pointer;"></i> </div></div></div></div>';
                                }else {
                                    sl_value++;
                                    html +='<div class="item_list item_list_'+ind+'"><div class="row">&nbsp;</div><div class="row"><div class="form-group item_list" id="value"> <label for="value_'+ind+'" class="col-form-label col-md-3"></label> <div class="col-md-2"> <input type="text" id="value_'+ind+'" name="value[]" value="'+itemVal.value+'" class="form-control" required/> </div> <div class="col-md-2"> <select id="color_'+ind+'" name="color[]" class="form-control" required><option value="red" '+((itemVal.color == 'red')? "selected": "")+'>Red</option><option value="yellow" '+((itemVal.color == 'yellow')? "selected": "")+'>Yellow</option><option value="green" '+((itemVal.color == 'green')? "selected": "")+'>Green</option><option value="black" '+((itemVal.color == 'black')? "selected": "")+'>Black</option></select> </div><div class="col-md-1" style="padding-top: 8px;"> <i class="fa fa-minus" aria-hidden="true" style="cursor:pointer;" onclick="removeItem('+ind+')"></i> </div></div></div></div>';
                                }
                            });
                        }else{
                            sl_value=1;
                            html ='<div class="item_list item_list_0"><div class="row">&nbsp;</div><div class="row"><div class="form-group item_list" id="value"> <label for="value_0" class="col-form-label col-md-2">Criticality Level(s)<span class="mandatory">*</span></label> <div class="col-md-2"> <input type="text" id="value_0" name="value[]" class="form-control" required/> </div> <div class="col-md-2"> <select id="color_0" name="color[]" class="form-control" required><option value="red">Red</option><option value="yellow">Yellow</option><option value="green">Green</option><option value="black">Black</option></select> </div><div class="col-md-1" style="padding-top: 8px;">  <i class="fa fa-plus" onclick="addItem()" aria-hidden="true" style="cursor:pointer;"></i> </div></div></div></div>';
                        }
                        $('#dynamic_form_area').html(html);
                        $('.save-config').show();
                    }
                }
            });
        }
    });
    $(document).ready(function () {
        var default_employeesurvey = {!! json_encode($defaulTemplate) !!};
        if(default_employeesurvey>0){
            $("#default_employeesurvey").val(default_employeesurvey).select2();
        }else{
                    $("#default_employeesurvey").select2();

        }
    });

    $(document).on("click",".setdefault",function(e){
        e.preventDefault();
        if($("#default_employeesurvey").val()>0){
        $.ajax({
            url: "{{route('admin.dashboardConfiguration')}}",
            data: {'employeesurvey':$("#default_employeesurvey").val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            success: function (response) {
                var data = jQuery.parseJSON(response)
                if(data.code==200){
                    swal("Success",data.message,"success")
                }else{
                    swal("Warning",data.message,"warning")
                }
            }
        });  
        }else{
            swal("Warning","Choose any Employee Survey","warning")
        }

    })
</script>
@endsection
