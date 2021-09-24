
<style>
    .contenttitle{
        font-weight: bold;
        padding: 10px;
        padding-top: 18px !important;

    }
    .controlarea{
        padding: 10px 0px 10px 10px;

    }
    .inlineblock{
        display: inline-block;
    }
    .rightblock{
        /* margin-right:5px !important; */
        float: right;
    }
    .tailor{
        width: 100% !important;
    }
</style>

<div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6" style="text-align: center">
        <img src="{{asset("images/measurement-chart.jpg")}}" class="tailor" alt="" srcset="">
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
        <div class="container-fluid" style="padding: 5px">
            <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 controlarea">

               </div>
               <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 contenttitle">
                Measurements (Inches)
           </div>


            </div>
            <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 contenttitle"
                    style="text-align: right">
                        Gender
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 controlarea">
                        <select name="gender"
                            id="gender"
                            class="form-control inlineblock" required>
                            <option value="">Select any</option>
                            <option value="male" @if ($candidateJob->candidate->gender=="male")
                                selected
                            @endif>Male</option>
                            <option value="female" @if ($candidateJob->candidate->gender=="female")
                                selected
                            @endif>Female</option>
                        </select>
                    </div>
            </div>
            @foreach ($lookups["uniformcontrolLookups"] as $uniformcontrol)
            @php
            $controlfor="male";
            $decimalvalue ="0.0";
            if(isset($uniformdetails[$uniformcontrol->id])){
                $uniformmeasure = explode(".",$uniformdetails[$uniformcontrol->id]);
                if(strlen($uniformmeasure[1])==2){
                    $decimalvalue = "0.".$uniformmeasure[1]."0";
                }else if(strlen($uniformmeasure[1])==1){
                    $decimalvalue = "0.".$uniformmeasure[1]."00";
                }else{
                    $decimalvalue = "0.".$uniformmeasure[1];
                }

            }
            if ($uniformcontrol->id==6) {
                $controlfor="female";
            }
        @endphp
                <div class="row {{$controlfor}}">
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 contenttitle"
                    style="text-align: right">
                        {{$uniformcontrol->name}}
                    </div>

                    <div
                    class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 controlarea ".$controlfor>
                    {{Form::selectRange('uniformcontrol-'.$uniformcontrol->id, 5, 70,
                    isset($uniformdetails[$uniformcontrol->id])?explode(".",$uniformdetails[$uniformcontrol->id])[0]:"",
                    array('placeholder'=>'Please select',
                    'class'=>'form-control col-sm-5 inlineblock '.$controlfor.' '.$controlfor.'control','id'=>'','required'=>'true'))}}
                     {{Form::select('point_decimal_value_'.$uniformcontrol->id,
                     config('globals.uniform_measurement_decimal_points'),$decimalvalue,
                     array('class'=>'form-control col-sm-5 inlineblock rightblock '.$controlfor.' '.$controlfor.'control','id'=>''))}}

                        <div class="form-control-feedback">

                            <span class="help-block text-danger align-middle font-12"></span>
                        </div>
                    </div>
                    <label for="uniformcontrol-{{$uniformcontrol->id}}"
                    class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 ">
                    </label>


                </div>
            @endforeach
            <div class="row">
                <div class="col-sm-3 contenttitle"></div>
                <div class="col-sm-3 contenttitle">
                    <label for="same_address_check" class="control-label">@lang('Same as Address')&nbsp;</label>
                    {{ Form::checkbox('same_address_check',null,null, array('id'=>'check_same_address')) }}<br>

                    </div>
            </div>

            <div class="row">
                <div class="col-sm-3 contenttitle"
                    style="text-align: right">
                        Shipping Address
                    </div>
                    <div class="col-sm-8"  id="shipping_address">
                        <textarea class="form-control" name="shipping_address"  rows="3" maxlength="1000">{{$candidateJob->candidate->shipping_address}}</textarea>
                    </div>
            </div>

        </div>
    </div>
</div>
@include('hranalytics::candidate.application.uniform_measurement_scripts')
