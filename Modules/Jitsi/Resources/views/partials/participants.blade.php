@foreach ($participants as $item)
    <div class="row" style="padding:0;padding-left:0px;padding-bottom:0px">
        <div class="col-md-12" style="padding-top: 3px">
            <p style="display:inline-block">
                @if ($item->ConferenceUser->employee->image=="")
                <img style="width: 70px;height: 70px;"
                    src="{{asset('images/uploads/') }}/{{ config('globals.noAvatarImg') }}" alt="">
            @else
                <img style="width: 70px;height: 70px;" src="{{asset("images/uploads/".$item->ConferenceUser->employee->image)}}" alt="">
            @endif
            </p>
            <p style="padding:0;display:inline-block;vertical-align: top"> {{$item->ConferenceUser->getFullNameAttribute()}}
            @if ($item->jitsiuserid=="")
            <span style="cursor: pointer;" attr-userid="{{$item->ConferenceUser->id}}"
                class="removeuser">[Remove]</span>
            @else
                <span>Joined</span>
            @endif

            </p>
        </div>
    </div>
@endforeach
