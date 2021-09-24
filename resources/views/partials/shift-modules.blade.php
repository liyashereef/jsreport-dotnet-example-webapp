@foreach($modules as $key=>$each_module)
@if($each_module->dashboard_view == 1)
<div id="mod" class="col-md-6 card-padding card-padding-top">

<div class="card-table">
    <div class="card-header">
    <img src="{{ asset('images/candidate.png') }}" style="width: 21px;"><span class="pl-2">{{$each_module->module_name}}</span>
    </div>

    <div  class="card-body table-responsive">
        <table  id="modu-{{$each_module->id}}" class="table js-customer-filter" style="width:100%">
            <thead id="{{$each_module->id}}">
               
            </thead>
        </table>
    </div>
</div>
</div>
@endif
@endforeach


