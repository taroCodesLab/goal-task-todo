@props(['title' => ''])

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="container">
        {{ $slot }}
    </div>
@endsection