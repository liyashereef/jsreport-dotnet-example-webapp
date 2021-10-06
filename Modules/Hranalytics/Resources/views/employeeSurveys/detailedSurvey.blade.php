@extends('layouts.app')
<style>
    
   
</style>
@section('content')
<!-- Container - Start -->

    <!-- Add document - Start -->

  {{ Form::open(array('url'=>'','id'=>'add-document-form', 'method'=> 'POST','autocomplete'=> "off")) }}
    {{ Form::hidden('document_type_id', isset($typeid) ? old('type_id',$typeid) : null,array('id'=>'type_id')) }}
  
  {{-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head document-screen-head">
    
    
      
      
  </div>
   --}}
<div class="table_title">
    <h4> Employee Survey</h4>
</div>
    <div class="timesheet-filters mb-2">
    <div class="row">
        <div class="col-md-3">
            <div class="row"  style="padding-left: 20px;">
                <div class="col-md-3"><label class="filter-text">Survey Name</label></div>
                <div class="col-md-8">
                    <input type="text" name="survey" class="form-control" value="{{ $data->survey->survey_name }}" readonly>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="filter-text">Customer Name</label>
                </div>
                <div class="col-md-8">
                     <input type="text" name="survey" class="form-control" value="{{ $data->customer->client_name ?? '--'}}" readonly>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="filter-text">Survey  By</label>
                </div>
                <div class="col-md-8">
                     <input type="text" name="survey" class="form-control" value="{{ $data->user->full_name }}" readonly>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-3">
                    <label class="filter-text">Date & Time</label>
                </div>
                <div class="col-md-8">
                     <input type="text" name="survey" class="form-control" value="{{ date_format($data->created_at,"d F Y")}}, {{ (date_format($data->created_at,"g:i A"))}}" readonly>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
    </div>
</div>
     
         <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-4" style="color: #f26222;
     font-weight: bold;">Survey Entries</label>
    <div class='layout'>
  
    @foreach($data->surveyAnswer as $key=> $surveyAnswer)
        <div class="form-group row"  id="document_category_id">
             <label for="document_category_id" class="col-sm-4 col-form-label">{{ $key+1 }} . {{ $surveyAnswer->question }}</label>
             <div class="col-sm-6">
            @if($surveyAnswer->answer_type==1)
                 {{ ucfirst($surveyAnswer->answer)}}
             @else
                 {{ $surveyAnswer->surveyRating->rating or '--'}}
            @endif

             <div class="form-control-feedback">{!! $errors->first('document_category_id') !!}
                   <span class="help-block text-danger align-middle font-12"></span>
            </div>
                </div>
             </div>
    
    @endforeach

        <div class="form-group row" style="padding-top: 50px;">
            <div class="col-sm-4 col-form-label">
               {{ Form::button('Cancel', array('class'=>'btn cancel', 'type'=>'reset','onClick'=>'window.history.back();'))}}
             
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

<!-- Container - End -->
<style type="text/css">
    #description{
        margin-top: 0px;
        height: 120px;
        margin-bottom: 0px;
    }
.layout{
    margin-left: -35px;
}
  #timesheet-tabs {
        margin: 0px 0px 3px 1px;
    }

    #timesheet-tabs .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #f48452;
    }
    .timesheet-filters{
        background: #f9f1ec;
        padding: 11px 5px;
    }
    .timesheet-filters .filter-text{
        position: absolute;
        top: 1;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    </style>
@stop
@section('scripts')

@stop
