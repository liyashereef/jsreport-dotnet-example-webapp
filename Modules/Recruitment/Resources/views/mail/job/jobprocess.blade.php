@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<h3> Job requisition process updates  </h3>

The Job Ticket(ID-{{$job->unique_key}}) assigned to <b>{{$job->assignee!=null ? $job->assignee->full_name: '<no one assigned yet >'}}</b> for '{{$job->positionBeeingHired->position}} at {{$job->customer->address}},{{$job->customer->city}},{{$job->customer->postal_code}}' has been
@if($job->status=='completed')
completed.
@else
started and progress updated.
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
