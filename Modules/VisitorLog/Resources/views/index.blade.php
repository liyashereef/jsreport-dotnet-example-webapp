@extends('visitorlog::layouts.master')

@section('content')
    <h1>Hello World</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <p>
        This view is loaded from module: {!! config('visitorlog.name') !!}
    </p>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        Echo.channel('home')
            .listen('NewMessage',(e)=>{
                console.log('e.message');
            })

            Echo.channel('visitor-log.129')
            .listen('CustomerDeviceUpdated',(e)=>{
                console.log('e.message');
            })
    </script>

@endsection
