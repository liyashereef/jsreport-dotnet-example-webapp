@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}

<h3> A new incident has been reported by {{ $model->reporter!=null?$model->reporter->full_name:'<none>' }} at {{ $model->customer!=null?$model->customer->client_name:'<none>'}} </h3>



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
