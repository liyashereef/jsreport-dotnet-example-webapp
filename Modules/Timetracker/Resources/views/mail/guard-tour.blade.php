@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}

<h3> Hi {{ $areaManager->full_name }}, </h3>
Guard '{{ $employeeShift->shift_payperiod->trashed_user->full_name }}' missed to submit Guard Tour for customer '{{ $employeeShift->shift_payperiod->trashed_customer->client_name }}'.
<!--Expected '{{ $guard_tour_counts['expected'] }}' shift journals. Submitted '{{ $guard_tour_counts['actual'] }}' shift journals.-->

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
