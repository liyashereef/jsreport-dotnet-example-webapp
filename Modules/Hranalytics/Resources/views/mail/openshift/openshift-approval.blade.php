@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}

<h3>You have been called upon for an Open Shift for the client {{$openshift['customer']['client_name']}} from {{$openshift['startdate']}}  {{$openshift['starttime']}} to {{$openshift['enddate']}}  {{$openshift['endtime']}} </h3>



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
