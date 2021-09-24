@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
@if(is_null($job->approved))
<h3> Job Requisition has been Created </h3>

A new Job posting for {{$job->positionBeeingHired->position}} at {{$job->customer->address}},{{$job->customer->city}},{{$job->customer->postal_code}}(ID-{{$job->unique_key}}) has been created.
@elseif($job->approved)
<h3> Job Requisition has been approved </h3>

Your Job posting for {{$job->positionBeeingHired->position}} at {{$job->customer->address}},{{$job->customer->city}},{{$job->customer->postal_code}}(ID-{{$job->unique_key}}) has been approved.

@else

<h3> Job Requisition has been rejected </h3>

Your Job posting for {{$job->positionBeeingHired->position}} at {{$job->customer->address}},{{$job->customer->city}},{{$job->customer->postal_code}}(ID-{{$job->unique_key}}) has been rejected.

@endif



{{-- Subcopy --}}
@slot('subcopy')
@component('mail::subcopy')
<!-- subcopy here -->
@endcomponent
@endslot


{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<!-- footer here -->
&copy; {{ date('Y') }} CGL 360.

@endcomponent
@endslot
@endcomponent
