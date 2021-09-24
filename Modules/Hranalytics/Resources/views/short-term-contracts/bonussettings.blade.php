@extends('layouts.app')
@section('css')
<style>
    th{
        color: #fff !important;
    }
</style>
    
@endsection
@section('content')
<form action="{{route("stc.savebonussettings")}}" id="bonusetting" method="post">
@csrf
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

    <div class="table_title">
        <h4> Bonus Settings </h4>
    </div>

    <div class="d-flex flex-row mb-3">
        <div class=" col-md-1 pr-2 pt-2">Start Date</div>
        <div class=" col-md-2 ">
            <input type="hidden" name="ongoingprogram" id="ongoingprogram"
             value="{{isset($settingsData)?$settingsData->id:0}}" />
             <input type="hidden" name="editprogram" id="editprogram"
             value="{{isset($settingsData)?$settingsData->id:0}}" />
            <input type="hidden" name="finalize" id="finalize" value="0" />
            <input type="hidden" name="recalculate" id="recalculate" value="0" />
            <input class="form-control datepicker" type="text"
             name="start_date" id="start_date" 
             @if (isset($settingsData))
             value="{{$settingsData->start_date}}"
             @else
             value="{{date("Y-m-d")}}"
             @endif
             
              />
        </div>
        <div class=" col-md-1 pl-2 pr-2 pt-2" style="text-align: right">End Date </div>
        <div class=" col-md-2 ">
            <input class="form-control datepicker" type="text" name="end_date" id="end_date" 
            @if (isset($settingsData))
               value="{{$settingsData->end_date}}"
             @else
             value="{{\Carbon::parse(date("Y-m-d"))->addDays(90)->format("Y-m-d")}}"
            @endif
            
             />
        </div>
    
      </div>
      <div class="d-flex flex-row mb-3">
        <div class=" col-md-1 pr-2 pt-2">Bonus Amount</div>
        <div class=" col-md-2 ">
            <input class="form-control " required type="number" id="bonus_amount" name="bonus_amount"
            @if (isset($settingsData))
               value="{{$settingsData->bonus_amount}}"
             @else
             value=""
            @endif
              />
        </div>
      </div>
      <div class="d-flex flex-row mb-3">
        <div class=" col-md-1 pr-2 pt-2">Wage Cap (%)</div>
        <div class=" col-md-2 ">
            <input class="form-control " required type="number" id="wagecap_percentage" name="wagecap_percentage" 
            @if (isset($settingsData))
            value="{{$settingsData->wagecap_percentage}}"
            @else
            value=""
            @endif             />
        </div>
        <div class=" col-md-1 pr-2 pt-2" style="text-align:right">Shift Cap (%)</div>
        <div class=" col-md-2 ">
            <input class="form-control " required type="number" id="shiftcap_percentage" name="shiftcap_percentage" 
            @if (isset($settingsData))
            value="{{$settingsData->shiftcap_percentage}}"
            @else
            value=""
            @endif  />
        </div>
        <div class=" col-md-1 pr-2 pt-2" style="text-align:right">Notice Cap (%)</div>
        <div class=" col-md-2 ">
            <input class="form-control " required type="number" id="noticecap_percentage" name="noticecap_percentage" 
            @if (isset($settingsData))
            value="{{$settingsData->noticecap_percentage}}"
            @else
            value=""
            @endif             />
        </div>
      </div>
      <div class="d-flex flex-row mb-3">
        <div class=" col-md-1 pr-2 pt-2"></div>
       
        <div class="col-md-2">
            @if (isset($settingsData))
                <input class="form-control btn btn-primary finalize" id="finalize" type="button" value="Edit">
            @else
            <input class="form-control btn btn-primary" type="submit" value="Create a Program">
            @endif


        </div>
        
      </div>
      <div class="d-flex flex-row mb-3">
        <div class=" col-md-1 pr-2 pt-2"></div>
       
      <div class=" col-md-2 ">
        @if (isset($settingsData))
            <input class="form-control btn btn-primary recalculate" type="submit" value="Recalculate">
        

        @endif
    </div>
      </div>
</form>

@endsection

@section('scripts')
 @include('hranalytics::short-term-contracts.partials.bonus-script')
@endsection
