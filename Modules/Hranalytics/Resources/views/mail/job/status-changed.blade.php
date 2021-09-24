@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}

<h3>Your Job ticket for {{$job->customer->client_name}} has been {{$job->status}} by {{$job->approver->first_name}}@if($job->approver->last_name !='') {{ucfirst($job->approver->last_name)}}. @else.@endif </h3>

Your Job Ticket (ID-{{$job->unique_key}}) for {{$job->customer->client_name}} has been {{$job->status}} by {{$job->approver->first_name}}@if($job->approver->last_name !='') {{ucfirst($job->approver->last_name)}}. @else.@endif 

 

Please login to your CGL360 account for more information. 


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
