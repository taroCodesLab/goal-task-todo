@extends('layouts.app')

@section('title', '403 Forbidden')

@section('content')
<div class="error-page text-center py-16">
    <h1 class="text-6xl font-bold mb-4">403</h1>
    <p class="text-lg mb-8">{{ __('messages.error_403') }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary">{{ __('messages.back_to_home') }}</a>
</div>
@endsection