@extends('adminlte::page')
@section('title', 'Welcome, Administrator ')
@section('content_header')
<h1>Hi, {{ Auth::user()->full_name }}</h1>
@stop
@section('content')
    <h3>Welcome to CGL360 Administration!</h3>   
    <img class="landing-img" src="{{ asset('images/landing-page-cgl360.jpg') }}" width="100%" />
@stop
