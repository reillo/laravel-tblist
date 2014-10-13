@extends('partials._layout')

@section('main-content-header')
<h1>Page Title</h1>
@overwrite

@section('main-content')
    {{ get_messages() }}

    @include('user._tblist')
@stop

