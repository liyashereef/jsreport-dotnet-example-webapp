    <!--Start- Slot Scheduling Form --->
    <div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'scheduling-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                <br>
                <div class="modal-body">
                    <div class="form-group row" id="first_name">
                        <label for="first_name" class="col-sm-3">Slot Details</label>
                        <div class="col-sm-9">
                            <label id="slotDetails" class="control-label view-form-element"></label>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="first_name">
                        <label for="first_name" class="col-sm-3">First Name</label>
                        <div class="col-sm-9">
                            {{ Form::label('first_name',null,array('placeholder'=>'First Name','class'=>'form-control', 'id'=>'firstName', 'readonly'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="last_name">
                        <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                        <div class="col-sm-9">
                            {{ Form::label('last_name',null,array('placeholder'=>'Last Name','class'=>'form-control', 'id'=>'lastName', 'readonly'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="email">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            {{ Form::email('email',null,array('placeholder'=>'Email','class'=>'form-control', 'id'=>'emailText', 'required'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="phone_number">
                        <label for="phone_number" class="col-sm-3 control-label">Phone</label>
                        <div class="col-sm-9">
                            {{ Form::text('phone_number',null,array('placeholder'=>'Phone [ format (XXX)XXX-XXXX ]','class'=>'form-control phone-mask','maxlength'=>6,'id'=>'phoneNumber', 'required'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="gender">
                        <label for="gender" class="col-sm-3 control-label">Gender</label>
                        <div class="col-sm-9">
                            {{ Form::select('gender',[null=>'Please select',1=>'Male',2=>'Female'],null,array('class'=>'form-control','id'=>'genderValue', 'required'=>'true')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" >
                        <div class="col-sm-12">
                            <img style="width: 103%;" src="{{asset("images/measurement-chart.jpg")}}" alt="" srcset="">
                        </div>
                    </div>
                    @foreach ($measurementPoints as $measurement)
                        <div class="form-group row" id="point_value_{{$measurement->id}}">
                            <label for="point_value_{{$measurement->id}}" id="label_{{$measurement->id}}" class="col-sm-3 control-label">{{$measurement->name}} </label>
                            <div class="col-sm-9 row" style="margin-left: 0px;">
                            {{ Form::hidden('point_ids[]', $measurement->id ) }}
                            {{Form::selectRange('point_value_'.$measurement->id, 5, 70,null,array('placeholder'=>'Please select','class'=>'form-control col-sm-5','id'=>'','required'=>'true'))}}
                            {{Form::select('point_decimal_value_'.$measurement->id, config('globals.uniform_measurement_decimal_points'),null,array('class'=>'form-control col-sm-4 offset-sm-1','id'=>''))}}
                            <small class="help-block"></small>
                            </div>
                        </div>
                    @endforeach

                    <!--Start-- Custom Question  Section -->
                    <div class="custom-questions-container">
                        @foreach($questions as $q)
                            <div id="{{$q['id']}}" class="form-group row">
                            {{ Form::hidden('question_ids[]', $q['id'] ) }}
                                <label for="{{$q['id']}}" class="col-sm-12">{{$q['question']}}</label>
                                <div class="col-sm-12">
                                    <select id="question_id_{{$q['id']}}" data-questionId="{{$q['id']}}" name="selected_option_id_{{$q['id']}}"  @if((int)$q['is_required'] == 1) required="TRUE" @endif
                                    class="form-control questionSelect"  >
                                        <option value="">Please Select</option>
                                        @foreach($q['options'] as $option)
                                        <option value="{{$option['id']}}">{{$option['custom_question_option']}}</option>
                                        @endforeach
                                    </select>
                                    <input placeholder="Please Specify" class="form-control other-option-text" id="option-text-{{$q['id']}}" name="other_option_vale_{{$q['id']}}" type="text" >
                                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                     <!--End-- Custom Question  Section -->

                </div>
                <div class="modal-footer" style="text-align: right;">
                    {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!--End- Slot Scheduling Form -->
