@extends('_partials._layout')

@section('content')
    <!-- Main Content -->
    <form action="{{ $list->getBaseURL() }}" method="get" class="tblist-form" autocomplete="off" id="users_tblist_form">
        @include('_partials._tblist')
    </form>
@stop

