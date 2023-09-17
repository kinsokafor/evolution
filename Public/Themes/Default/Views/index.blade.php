@extends('layouts.app')

@section('title', $pageTitle)

@section('sidebar')
    @parent
    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    {!! $content !!}
@endsection