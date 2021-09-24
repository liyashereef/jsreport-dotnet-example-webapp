{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Templates')

@section('content_header')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    .icn-low {
        color: gray;
    }

    .rm-field-btn {
        color: orangered;
    }

    .rm-field-btn:hover {
        color: orange;
    }
</style>

<h3>Visitor Log Templates</h3>
@stop

@section('content')
<div class="container-fluid container-wrap">
    {{ Form::open(array('route'=> 'visitorlog-templates.store','id'=>'template-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    <!-- Main content -->
    <section class="content">
        <div class="form-group row" id="template_name">
            <input type="hidden" name="id" value="{{$template[0]->id or ''}}" />
            <label class="col-form-label col-md-2" for="template_name">Template Name </label>
            <div class=" col-md-6">
                <input type="text" class="form-control" placeholder="Name" name="template_name" value="{{$template[0]->template_name or ''}}" required>
                <span class="help-block"></span>
            </div>
        </div>


        <h4 class="color-template-title">Template Fields</h4>
        <div class="table-responsive">
            <table id="table-template-fields" style="text-align: center;" class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info">
                <thead>
                    <tr>
                        <th class="sorting_disabled">#</th>
                        <th class="sorting_disabled">Field Name</th>
                        <th class="sorting_disabled">Display</th>
                        <th class="sorting_disabled">Mandatory</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="template-questions">
                    @foreach($basic_fields as $key => $eachfield)
                    <tr role="row" class="tf-row" id="tf-el-{{++$key}}">
                        <td>{{$key}}</td>
                        <input type="hidden" name="uid[]" value="{{$key}}" />
                        <input type="hidden" name="field_id_{{$key}}" value="{{$eachfield->id}}" />
                        <input type="hidden" name="field_type_{{$key}}" value="{{$eachfield->field_type}}" />
                        <input type="hidden" name="fieldname_{{$key}}" value="{{$eachfield->fieldname}}" />
                        <input type="hidden" name="is_custom_{{$key}}" value="{{$eachfield->is_custom}}" />
                        <td><input class="form-control" @if($eachfield->is_custom == 0) readonly @endif type="text" name="field_displayname_{{$key}}" value="{{$eachfield->field_displayname}}"></td>
                        <td><input type="checkbox" class="js-tfval" data-id="{{$eachfield->fieldname}}" value="{{$eachfield->is_visible}}" name="is_visible_{{$key}}" {{$eachfield->is_visible ? 'checked' : ''}}></td>
                        <td><input type="checkbox" class="js-tfval" data-id="{{$eachfield->fieldname}}" value="{{$eachfield->is_required}}" name="is_required_{{$key}}" {{$eachfield->is_required ? 'checked' : ''}}></td>
                        <td>
                            @if($eachfield->is_custom == 1)
                            <a href="javascript:void(0)" class="js-delete-field-btn rm-field-btn" data-target="#tf-el-{{$key}}">
                                <i class="fa fa-lg fa-minus-circle"></i>
                            </a>
                            @else
                            <i class="fa fa-lg fa-lock icn-low"></i></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td><button type="button" class="js-new-field-btn btn btn-primary blue"><i class="fa fa-plus"></i> Add</button></td>
                    </tr>
                </tfoot>
            </table>
        </div>


        <h4 class="color-template-title">Template Features</h4>
        <div class="table-responsive">
            <table style="text-align: center;" class="table table-bordered dataTable " role="grid" aria-describedby="position-table_info">
                <thead>
                    <tr>
                        <th class="sorting_disabled">#</th>
                        <th class="sorting_disabled">Feature Name</th>
                        <th class="sorting_disabled">Display</th>
                        <th class="sorting_disabled">Mandatory</th>
                    </tr>
                </thead>
                <tbody id="template-features">
                    @foreach($features as $featurekey => $eachfeature)
                    <tr role="row" class="tfeat-row">
                        <td>{{++$featurekey}}</td>
                        <input type="hidden" name="{{$featurekey}}_feature_id" value="{{$eachfeature->id}}" />
                        <input type="hidden" name="{{$featurekey}}_feature_name" value="{{$eachfeature->feature_name}}" />
                        <td><input class="form-control" readonly type="text" name="{{$featurekey}}_feature_displayname" value="{{$eachfeature->feature_displayname}}" /> </td>

                        <td><input type="checkbox" onclick="checkFRequired($(this).data('id'))" data-id="{{$featurekey}}" name="{{$featurekey}}_feature_is_visible" id="{{$featurekey}}_feature_is_visible" {{$eachfeature->is_visible ? 'checked' : ''}}></td>
                        <td><input type="checkbox" name="{{$featurekey}}_feature_is_required" onclick="checkFVisibility(this.id)" class="{{$featurekey}}_feature_is_required" id="{{$featurekey}}" {{$eachfeature->is_required ? 'checked' : ''}}></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <input type="hidden" name="feature_count" value="{{count($features)}}" />
        </div>


        <div class="modal-footer">
            <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
            <a href="{{ route('visitorlog.templates') }}" class="btn btn-primary blue">Cancel</a>
        </div>

    </section>
    {{ Form::close() }}
</div>
@stop
@section('js')
<script>
    const tscript = {
        data: {
            totalFields: 0,
        },
        onGenerateNewRow() {
            this.data.totalFields += 1;
            let fieldNo = this.data.totalFields;

            let row = `<tr role="row" class="tf-row tf-new-row" id="tf-el-${fieldNo}">
                        <td>${fieldNo}</td>
                        <input type="hidden" name="uid[]" value="${fieldNo}" />
                        <input type="hidden" name="field_id_${fieldNo}"/>
                        <input type="hidden" name="field_type_${fieldNo}" value="1"/>
                        <input type="hidden" name="is_custom_${fieldNo}" value="1"/>
                        <td><input type="text" placeholder="Enter field name" class="form-control"  name="field_displayname_${fieldNo}" required/></td>
                        <td><input type="checkbox" name="is_visible_${fieldNo}" value="0"/></td>
                        <td><input type="checkbox" name="is_required_${fieldNo} value="0"/></td>
                        <td>
                            <a href="javascript:void(0)" class="js-delete-field-btn rm-field-btn" data-target="#tf-el-${fieldNo}">
                                <i class="fa fa-lg fa-minus-circle"></i>
                            </a>    
                        </td>
                    </tr>`;
            $('#template-questions').append(row)
        },
        onDeleteRow(target) {
            $(target).remove();
        },
        onSubmitForm(){
            //Migrate old code to this section
        },
        initializeData() {
            this.data.totalFields = $('.tf-row').length;
        },
        init() {
            let root = this;
            this.initializeData();

            //On add new field
            $(".js-new-field-btn").on('click', function(e) {
                root.onGenerateNewRow();
            });

            //On delete a field
            $("body").on('click', '.js-delete-field-btn', function(e) {
                root.onDeleteRow($(this).data('target'));
            });

            $('.js-tfval').on('click', function() {
                let protectedFields = ['full_name', 'check_in'];

                if (protectedFields.includes($(this).data('id'))) {
                    console.log($(this).data('id'));
                    return false;
                }
            });
        }
    }

    $(function() {
        tscript.init();
    });


    $('#template-add-form').submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        url = "{{ route('visitorlog-templates.store') }}";

        let formData = new FormData($('#template-add-form')[0]);
        // Log the key/value pairs  
        for (var pair of formData.entries()) {
            console.log(pair[0] + ' - ' + pair[1]);
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function(data) {
                if (data.success) {
                    if (data.result == false) {
                        result = "Template has been updated successfully";
                    } else {
                        result = "Template has been created successfully";
                    }
                    swal({
                        title: "Saved",
                        text: result,
                        type: "success",
                        confirmButtonText: "OK",
                    }, function() {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        window.location.href = "{{ route('visitorlog.templates') }}";
                    });
                } else {
                    $('.form-group').addClass('has-error').find('.help-block').text(data.error['template_name'][0]);
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                }



            },
            fail: function(response) {
                console.log(data);
            },
            error: function(xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form, true);
            },
            contentType: false,
            processData: false,
        });
    });

    function checkVisibility(id) {
        $('#' + id + '_is_visible').attr('checked', true);
    }

    function checkRequired(id) {
        $('.' + id + '_is_required').prop('checked', false);
    }

    function checkFVisibility(id) {
        $('#' + id + '_feature_is_visible').attr('checked', true);
    }

    function checkFRequired(id) {
        $('.' + id + '_feature_is_required').prop('checked', false);
    }
</script>
@stop