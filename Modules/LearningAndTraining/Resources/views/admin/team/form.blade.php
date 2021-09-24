@extends('layouts.app')
@section('content')


@section('content')
    {{--    <div class="table_title">--}}
    {{--        <h4>Team Management</h4>--}}
    {{--    </div>--}}

    {{--    <div id="message"></div>--}}




    <div style="padding-right: 10px;">

        {{ Form::open(array('url'=>'#','id'=>'team-register-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{--    {{ csrf_token() }}--}}
        <div class="modal-dialog" style="margin:20px; ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title table_title" id="myModalLabel">Team Creation Form</h4>
                </div>

                <input name="team_id" type="hidden" value="@if(isset($team_details->id)){{$team_details->id}} @endif">
                <div class="modal-body">
                    <div class="form-group" id="severity">
                        <label for="severity" class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" id="name" name="name"
                                   value="@if(isset($team_details->name)){{$team_details->name}} @endif">
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="concern">
                        <label for="concern" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-11">
                            <textarea class="form-control" name="description" id="description" cols="50"
                                      rows="5">@if(isset($team_details->description)){{$team_details->description}}@endif</textarea>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="severity">
                        <label for="severity" class="col-sm-3 control-label">Select Parent Team</label>
                        <div class="col-sm-11">
                            <select class="form-control" name="parent_team_id" id="parent_team_id">
                                <option value="0">Default (This is a parent team)</option>
                                @foreach($teams as $team)
                                    @if($team->id != $id)
                                        <option value="{{$team->id}}"
                                                @if(isset($team_details->parent_team_id) && $team_details->parent_team_id == $team->id) selected @endif >{{$team->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="concern">
                        <label for="concern" class="col-sm-5 control-label">Mandatory Courses </label>
                        <div class="col-sm-11">
                            <select class="form-control js-example-basic-multiple"
                                    id="select-mandatory-course"
                                    name="mandatory_course[]" multiple="multiple">
                                @foreach($courses as $course)
                                    <option value="{{$course->id}}"
                                            @if(in_array($course->id,$mandatory_course_array)) selected @endif>{{$course->course_title}}</option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div>

                    <div class="form-group" id="concern">
                        <label for="concern" class="col-sm-5 control-label">Recommended Courses</label>
                        <div class="col-sm-11">
                            <select class="form-control js-example-basic-multiple"
                                    id="select-recommend-course"
                                    name="recommended_course[]" multiple="multiple">
                                @foreach($courses as $course)
                                    <option value="{{$course->id}}"
                                            @if(in_array($course->id,$recommended_course_array)) selected @endif>{{$course->course_title}}</option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input class="button btn btn-primary blue" id="mdl_save_change" type="button" value="Save">
                    <input class="btn btn-primary blue" type="button" value="Cancel" id="cancel_form">
                </div>

            </div>
        </div>

        {{ Form::close() }}
    </div>



@stop
@section('scripts')

    {{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet"/>--}}
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>--}}
    <link href="{{ asset('js/select2/select2.min.css') }}" rel="stylesheet">
    <script href="{{ asset('js/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#select-mandatory-course').select2();
            $('#select-recommend-course').select2()

            //Select Events
            $('#select-mandatory-course').on('change', function (e) {
                var selectedItems = $(this).val();
                var options = $('#select-recommend-course option');
                //remove all items
                options.each(function (index, item) {
                    $(item).attr('disabled', false)
                });
                //attach disabled field
                options.each(function (index, item) {
                    if (selectedItems.indexOf($(item).val()) > -1) {
                        $(item).attr('disabled', true)
                    }
                });
                $('#select-recommend-course').select2();

            });

            //Unselect Events
            $('#select-recommend-course').on('change', function (e) {
                var selectedRecItems = $(this).val();
                var options = $('#select-mandatory-course option');
                options.each(function (index, item) {
                    $(item).attr('disabled', false)
                });
                //remove all items
                options.each(function (index, item) {
                    if (selectedRecItems.indexOf($(item).val()) > -1) {
                        $(item).attr('disabled', true)
                    }
                });

                $('#select-mandatory-course').select2();
            });


            $('#select-mandatory-course').trigger('change');
            $('#select-recommend-course').trigger('change');
        });


        $('#mdl_save_change').on('click', function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($('#team-register-form')[0]);
            console.log(formData)

            var title = $('#name').val();
            if (title == '') {
                swal("Warning", "Name required", "warning");
                return false;
            }

            var url = "{{ route('learningandtraining.team.store') }}";

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    // if (data.message) {
                    //     swal("Oops", errors.name, "warning");
                    // }
                    if (data.success) {
                        swal({
                                title: "Saved",
                                text: "Team has been successfully saved",
                                type: "success"
                            },
                            function () {
                                // $("#myModal").modal('hide');
                                // location.reload(true);
                                window.location.replace("{{ route('learningandtraining.team.list.page') }}");
                            });
                    } else {
                        //alert('else');
                        // console.log(data);
                        swal("Oops", "The record has not been saved", "warning");
                    }
                },
                fail: function (response) {
                    //alert('response');
                    // console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                    // associate_errors(xhr.responseJSON.errors, $form);
                    if(xhr.responseJSON.errors.name){
                        swal("Oops", xhr.responseJSON.errors.name, "warning");
                    }
                    if(xhr.responseJSON.errors.description){
                        swal("Oops", xhr.responseJSON.errors.description, "warning");
                    }
                    
                },
                contentType: false,
                processData: false,
            });

        });


        $('#cancel_form').on('click', function (e) {
            // window.location.replace('/learningandtraining/teams');
            window.location.replace("{{ route('learningandtraining.team.list.page') }}");
        });



    </script>



@stop
