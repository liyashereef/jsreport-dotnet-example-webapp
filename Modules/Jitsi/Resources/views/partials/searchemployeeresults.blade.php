@foreach ($liveusers as $liveemployee)
<div class="emplist" style="border-bottom:solid 1px grey;">
    <p style="display: inline-block;width: 14%;vertical-align: middle;margin-top:0;margin-bottom:0">
        @if ($liveemployee->employee->image==null)
            <img style="width: 70px;height: 70px;"
                src="{{asset('images/uploads/') }}/{{ config('globals.noAvatarImg') }}" alt="">
        @else
            <img style="width: 70px;height: 70px;" src="{{asset("images/uploads/".$liveemployee->employee->image)}}" alt="">
        @endif
        </p>
            <p class="pagep" style="width: 85%;

            padding-bottom:3px;
            display: inline-block;vertical-align: middle;margin-top:0;margin-bottom:0">
                {{$liveemployee->getFullNameAttribute()}} <br/>
                {{isset($liveemployee->liveStatus->customer)?$liveemployee->liveStatus->customer->project_number."-".$liveemployee->liveStatus->customer->client_name:""}}
                <button type="button" attr-id="{{$liveemployee->id}}" id="{{$liveemployee->id}}"
                class="addliveemp btn btn-primary" style="float:right;right:35px;margin-top:-10px">Add</button>
            </p>
</div>

@endforeach
