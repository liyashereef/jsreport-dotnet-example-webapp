@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<h3> Job Requisition has been Assigned </h3>

The Job Ticket(ID-{{$job->unique_key}}) has been assigned to <b>{{$job->assignee->full_name}}</b>

Please begin recruiting process for {{$job->positionBeeingHired->position}} at {{$job->customer->address}},{{$job->customer->city}},{{$job->customer->postal_code}}.
The Posting has been approved by {{$job->approver->full_name}}.

The posting must be filled by no later than {{$job->required_job_start_date}}



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
