@foreach ($searchresult as $result)
<p>{{$result->getFullNameAttribute()}} -
    @if (isset($result->employee))
        {{$result->employee->employee_no}}
    @endif
    - <span style="cursor: pointer" attr-id="{{$result->id}}"
         class="addemployee">Add</span>

</p>
@endforeach
