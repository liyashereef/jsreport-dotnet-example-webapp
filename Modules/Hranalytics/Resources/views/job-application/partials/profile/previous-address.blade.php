@php
$id= app('request')->input('id');
@endphp

<div class="form-group row prev-address-container">
  <label for="prev_address" class="col-sm-5 col-form-label">Address</label>
    <div class="col-sm-7 form-group"  id="prev_address.{{$id}}">
        {{Form::text('prev_address[]',old("prev_address.".$id,isset($addres->address) ? $addres->address :""),array('class'=>'form-control ','placeholder'=>"Enter address,city,postal code"))}}
        <div class="form-control-feedback"> {!! $errors->first('prev_address.'.$id) !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
    <div  class="col-sm-5 row  offset-xl-5 additional-address" >
        <label for="prev_address" class="col-sm-2 col-form-label"><b>From</b></label>
        <div class="col-sm-4 form-group additional-address-from" id="prev_address_from.{{$id}}">
            {{Form::text('prev_address_from[]',old('prev_address_from.'.$id,isset($addres->from) ? $addres->from :""),array('class'=>'form-control datepicker','placeholder'=>"",'max'=>"2900-12-31",'readonly'=>"readonly"))}}
            <div class="form-control-feedback">{!! $errors->first('prev_address_from.'.$id) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
        <label for="prev_address" class="col-sm-2 col-form-label" ><b>To</b></label>
        <div class="col-sm-4 form-group additional-address-to" id="prev_address_to.{{$id}}">
            {{Form::text('prev_address_to[]',old('prev_address_to.'.$id,isset($addres->to) ? $addres->to :""),array('class'=>'form-control datepicker','placeholder'=>"",'max'=>"2900-12-31",'readonly'=>"readonly"))}}
            <div class="form-control-feedback">{!! $errors->first('prev_address_to.'.$id) !!}
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="col-sm-2 p0">
         <a class="remove-previous-adresses pull-right" href="javascript:void(0);"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
    </div>
</div>
