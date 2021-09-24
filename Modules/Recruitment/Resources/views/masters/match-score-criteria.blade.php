{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Match Score Criteria')

@section('content_header')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h3>Match Score Criteria</h3>
@stop

@section('content')
<div class="container-fluid container-wrap">

    <div class="modal-body">

        <h4 class="color-template-title">Criterias</h4>
        {{ Form::open(array('url'=>'#','id'=>'template-add-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        <div class="modal-body">
            <div class="form-group row table-bordered datatable">
                <div class="col-sm-4" align="center"><strong>Criteria Name</strong>
                </div>
                <div class="col-sm-2" align="center"><strong>Weight (in %)</strong>
                </div>
                <div class="col-sm-3 check" align="center"><strong>Type</strong>
                </div>
                <div class="col-sm-2 check" align="center"><strong>Action</strong>
                </div>
            </div>

            <div id="dynamic-rows">
            </div>

        </div>

        <div class="modal-footer">
            <div class="form-group row" id="template_name">
                <label class="col-form-label col-md-1" for="template_name">Total Weight (in %) </label>
                <div class=" col-md-2">
                    <input type="text" class="form-control" id="average" name="average" value="0" readonly>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input class="button btn btn-primary blue" id="mdl_save_change" type="submit" value="Save">
            <a href="#" onClick='location.reload()' class="btn btn-primary blue">Cancel</a>
        </div>


        {{ Form::close() }}

    </div>
    <div class="modal fade" id="textModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="modal-titles">Set Criteria:</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'text-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('criteria_id', null) }}
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-6"><strong>Criteria Name</strong></div>
                        <div class="col-sm-4"><strong>Score</strong></div>
                    </div>

                    <div class="body-content">
                        <div id="dynamic-rows2">
                        </div>

                    </div>
                    <div class="modal-footer">
                        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue'))}}
                        {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                    </div>
                    {{ Form::close() }}


                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="yesNoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="modal-titles">Set Criteria Range: Yes/No</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'yes-no-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('criteria_id', null) }}
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-4"><strong>Limit Range</strong></div>
                        <div class="col-sm-4"><strong>Score</strong></div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Yes
                            <small class="help-block"></small>
                        </div>
                        <div class="col-sm-4  text-center">
                            <input type="hidden" name="yes-step-id">
                            <input type="number" class="form-control clearForm" name="score_yes" required>
                            <small class="help-block"></small>
                        </div>
                        <div class="col-sm-2">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No
                            <small class="help-block"></small>
                        </div>


                        <div class="col-sm-4  text-center">
                            <input type="hidden" name="no-step-id">
                            <input type="number" class="form-control clearForm" name="score_no" required>
                            <small class="help-block"></small>
                        </div>
                        <div class="col-sm-2">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue'))}}
                    {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                </div>
                {{ Form::close() }}


            </div>
        </div>
    </div>


    <div class="modal fade" id="mappingModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="modal-title">Set Criteria Range</h4>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'mapping-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('criteria_id', null) }}
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-7"><strong>Limit Range</strong></div>
                        <div class="col-sm-3"><strong>Score</strong></div>

                    </div>
                    <div class="body-content">
                        <div id="dynamic-rows1">
                        </div>
                        <div class="form-group">
                            <div class="col-sm-7">
                                <input type="hidden" name="over_step_id" id="over_step_id">
                                Over&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="text" class="form-control clearForm maxupto lower" style="display: inline;width:15%;" id="over_limit" name="over_limit" value="" readonly />&nbsp;
                                <span></span>
                                <small class="help-block"></small>
                            </div>

                            <div class="col-sm-3  text-center">
                                <input type="number" class="form-control clearForm" name="over_score" required id="over_score">
                                <small class="help-block"></small>
                            </div>
                            <div class="col-sm-2">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'recipientSubmit'))}}
                    {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                </div>
                {{ Form::close() }}


            </div>
        </div>
    </div>


</div>
@include('recruitment::partials.job-criteria-partials')
@include('recruitment::partials.moreSteps')
@include('recruitment::partials.text-type-partials')
@stop


@section('js')
<script src="{{ asset('js/moreel.js') }}"></script>
<script>
    $(function() {




        let criteria = <?php echo json_encode($criteria); ?>;
        let score_lookups = <?php echo json_encode($score_lookups); ?>;      
        let score_type = <?php echo json_encode($score_type); ?>;
        console.log(score_type, criteria)
        let type = <?php echo json_encode(config('globals.match_type')); ?>;
        var arr_type = new Array("", "yes/no", "year", "%", "minutes", 'text','value');
        let divParam = {
            containerDiv: '#dynamic-rows',
            addButton: '.step_add_button',
            removeButton: '.step_remove_button',
            form: '#template-add-form',
        };
        let moreSteps = new MoreEl('step', divParam);
        if (criteria.length != 0) {
            moreSteps.initElDiv(true);

            for (let i = 0; i < criteria.length; i++) {
                let idSelector = 'input[name="step-id[' + i + ']"]';
                let criteriaSelector = 'select[name="criteria_name[' + i + ']"] option[value="' + criteria[i].criteria_id + '"]';
                let weightSelector = 'input[name="weight[' + i + ']"]';
                let typeIdSelector = 'select[name="type_id[' + i + ']"] option[value="' + criteria[i].score_criteria_lookup.type_id + '"]';
                let mappingIdSelector = '#mapping_' + i + '';
                let id = criteria[i].criteria_id;
                let criteria_name = criteria[i].criteria_id;
                let weight = criteria[i].weight;
                let type_id = criteria[i].score_criteria_lookup.type_id;
                let newSteps = moreSteps.addRow();
                $(newSteps).find(idSelector).val(id);
                $(newSteps).find(criteriaSelector).prop('selected', true);
                $(newSteps).find(weightSelector).val(weight);
                $(newSteps).find(typeIdSelector).prop('selected', true); 
                $(newSteps).find(mappingIdSelector).show();
                if(type_id==6){
                $(newSteps).find(mappingIdSelector).attr('title', 'Mapping cannot be set for this type');
                $(newSteps).find(mappingIdSelector).removeClass('mapping'); 
                $(newSteps).find(mappingIdSelector).removeAttr('href');
                }
                $(newSteps).find(mappingIdSelector).attr('data-mapid', id);
                $(newSteps).find(mappingIdSelector).attr('data-maptype', arr_type[type_id]);
                weightCalculator();
            }
        } else {

            moreSteps.initElDiv();
        }

        $('#yes-no-form').submit(function(e) {
            e.preventDefault();
            var $form = $('#yes-no-form');
            var formData = new FormData($('#yes-no-form')[0]);
            $('.form-group').removeClass('has-error').find('.help-block').text('');
            url = "{{ route('recruitment.match-score-criteria-mapping.store') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    if (data.success) {
                        swal({
                                title: "Saved",
                                text: "Mapping has been created successfully",
                                type: "success"
                            },
                            function() {
                                location.reload();
                            }
                        );
                    } else {
                        console.log(data.success);
                    }
                },
                fail: function(response) {
                    console.log(response);
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });
        $('#mapping-form').submit(function(e) {
            e.preventDefault();
            var $form = $('#mapping-form');
            var formData = new FormData($('#mapping-form')[0]);
            $('.form-group').removeClass('has-error').find('.help-block').text('');
            url = "{{ route('recruitment.match-score-criteria-mapping.store') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    if (data.success) {
                        swal({
                                title: "Saved",
                                text: "Mapping has been created successfully",
                                type: "success"
                            },
                            function() {
                                location.reload();
                            }
                        );
                    } else {
                        console.log(data.success);
                    }
                },
                fail: function(response) {
                    console.log(response);
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });
        $('#text-form').submit(function(e) {
            e.preventDefault();
            var $form = $('#text-form');
            var formData = new FormData($('#text-form')[0]);
            $('.form-group').removeClass('has-error').find('.help-block').text('');
            url = "{{ route('recruitment.match-score-criteria-mapping.store') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    if (data.success) {
                        swal({
                                title: "Saved",
                                text: "Mapping has been created successfully",
                                type: "success"
                            },
                            function() {
                                location.reload();
                            }
                        );
                    } else {
                        console.log(data.success);
                    }
                },
                fail: function(response) {
                    console.log(response);
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        });
        $('#template-add-form').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('recruitment.match-score-criteria.store') }}";
            var formData = new FormData($('#template-add-form')[0]);
            if ($('#average').val() != 100) {
                swal("Alert", "The total of weights must be 100", "warning");
                return false;
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
                            window.location.href = "{{ route('recruitment.match-score-criteria') }}";
                        });
                    } else {
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        console.log(data);
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


        $("#template-add-form").on("input", ".weightage", function(e) {
            weightCalculator();
        });


        $(".step_remove_button").click(function() {
            weightCalculator();
        });


        $("#template-add-form").on("click", ".mapping", function(e) {
            $('#mappingModal,#yesNoModal').find('.clearForm').val('')
            var criteriaid = $(this).data('mapid')
            var criteriatype = $(this).data('maptype')
            var url = '{{ route("recruitment.match-score-criteria-mapping.list",":id") }}';
            var url = url.replace(':id', criteriaid);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    console.log(data.tooltip)
                    if (criteriatype == 'text') {
                        let divParam2 = {
                            containerDiv: '#dynamic-rows2',
                            addButton: '.add_button',
                            removeButton: '.text_remove_button',
                            form: '#text-form',
                            moreHtmlDiv: '#more-text-content',
                            removeOne: true,
                        };
                        let moreSteps1 = new MoreEl('step2', divParam2);
                        $('#textModal').modal();
                        if (data.result.length != 0) {
                            moreSteps1.initElDiv(true);
                        } else {
                            moreSteps1.initElDiv();
                        }
                        console.log()
                        for (let i = 0; i < data.result.length; i++) {
                            let low = data.result[i].limit;
                            let score = data.result[i].score;
                            let id = data.result[i].id;
                            let lowSelector = 'input[name="lower_limit[' + i + ']"]';
                            let scoreSelector = 'input[name="score[' + i + ']"]';
                            let idSelector = 'input[name="step-id[' + i + ']"]';

                            let newSteps1 = moreSteps1.addRow();
                            $(newSteps1).find(idSelector).val(id);
                            $(newSteps1).find(lowSelector).val(low);
                            $(newSteps1).find(lowSelector).attr('title',data.tooltip[i+1]);
                            $(newSteps1).find(scoreSelector).val(score);
                        }
                        $('#text-form').find('input[name="criteria_id"]').val(criteriaid)
                        $('#textModal .modal-title').text("Set Criteria Range: " + score_lookups[criteriaid]);
                    } else if (criteriatype == 'yes/no') { //Check Yes/No type
                        $('#yesNoModal').modal();
                        $('#yes-no-form').find('input[name="criteria_id"]').val(criteriaid)
                        $('#yes-no-form').find('.form-group span').text(arr_type[score_type[criteriaid]]);
                        $('#yesNoModal .modal-title').text("Set Criteria Range: " + score_lookups[criteriaid])
                        $.each(data.result, function(index, value) {
                            if (value.limit == 1) {
                                $('#yes-no-form').find('input[name="score_yes"]').val(value.score);
                                $('#yes-no-form').find('input[name="yes-step-id"]').val(value.id);

                            } else {
                                $('#yes-no-form').find('input[name="score_no"]').val(value.score);
                                $('#yes-no-form').find('input[name="no-step-id"]').val(value.id);
                            }
                        });

                    } else {
                        let divParam1 = {
                            containerDiv: '#dynamic-rows1',
                            addButton: '.add_button',
                            removeButton: '.remove_button',
                            form: '#mapping-form',
                            moreHtmlDiv: '#more-step-content',
                            removeOne: true,
                        };
                        let moreSteps1 = new MoreEl('step1', divParam1);
                        $("#mappingModal").modal();
                        
                        $('#mappingModal .modal-title').text("Set Criteria Range: " + score_lookups[criteriaid])
                        if (data.result.length != 0) {
                            moreSteps1.initElDiv(true);
                        } else {
                            moreSteps1.initElDiv();
                        }
                        
                        for (let i = 0; i < data.result.length; i++) {
                            let low = data.result[i].limit
                            let score = data.result[i].score
                            let id = data.result[i].id
                            var isLastElement = i == data.result.length - 1;
                            if (isLastElement) {

                                $('input[name="upper_limit[' + (i - 1) + ']"]').val(low);
                                $('#over_limit').val(low);
                                $('#over_score').val(score);
                                $('#over_step_id').val(id);

                            } else {
                                let lowSelector = 'input[name="lower_limit[' + i + ']"]';
                                let scoreSelector = 'input[name="score[' + i + ']"]';
                                let idSelector = 'input[name="step-id[' + i + ']"]';

                                let newSteps1 = moreSteps1.addRow();
                                $(newSteps1).find(idSelector).val(id);
                                $(newSteps1).find(lowSelector).val(low);
                                $(newSteps1).find(scoreSelector).val(score)
                                $('input[name="upper_limit[' + (i - 1) + ']"]').val(low);
                            }
                        }
                        $('#mapping-form').find('input[name="criteria_id"]').val(criteriaid)

                        $('#mapping-form').find('.form-group span').text(arr_type[score_type[criteriaid]]);
                    }

                }
            });
        });


        $("#mappingModal").on("click", ".add_button", function() {
            adjustMinAndNextValues();
        });

        $(document).on('click', '.remove_button', function(e) {
            adjustMinAndNextValues();
        });
        $("#mapping-form").on("input", ".maxupto", function(e) {
            adjustMinAndNextValues();
        });


    });

    function weightCalculator() {
        var s1 = $('.weightage');
        var val = 0;
        $.each(s1, function(index, value) {
            if (!isNaN(parseInt($(value).val()))) {
                val = parseInt(val) + parseInt($(value).val());
            }
        });
        $('#average').val(val)
    }

    function updateResponseTime(result) {
        var index = (result.id).split('_')[1];
        next_index = parseInt(index) + parseInt(1);
        $('#lower_' + next_index).val(result.value);
        if ($('#dynamic-rows1 .el_fields:last').find('.maxupto')[0] == result)
            $('#over_limit').val(result.value);
    }

    function adjustMinAndNextValues() {
        let each_value = 0;
        $('.body-content').find('.form-group').each(function(index, row) {
            $(row).find('.lower').val(each_value)
            $(row).find(".maxupto").attr('min', parseInt(each_value) + 1)
            each_value = $(row).find('.maxupto').val();
            $('#lower_0').val(0);
        });
    }

    function populateType(item) {
        let score_type = <?php echo json_encode($score_type); ?>;
        $(item).closest('.el_fields').find('.type').val(score_type[item.val()])

    }
</script>
<style>
    .event {
        z-index: 999;
    }
</style>
@stop
