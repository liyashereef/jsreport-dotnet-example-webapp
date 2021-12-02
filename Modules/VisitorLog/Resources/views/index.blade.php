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

            Echo.channel('private-visitor-log-device.08cd85ba-935a-4d14-9fde-4ecf29f160a5')
            .listen('CustomerDeviceUpdated',(e)=>{
                console.log('e.message');
            })
    </script>

@endsection
