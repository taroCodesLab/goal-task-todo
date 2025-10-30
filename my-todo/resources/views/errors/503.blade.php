
@extends('layouts.app')

@section('title', 'Service Unavailable')

@section('content')
<div class="error-page text-center py-16">
    <h1 class="text-6xl font-bold mb-4">503</h1>
    <p class="text-lg mb-8">{{ __('messages.error_503') }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-6">{{ __('messages.back_to_home') }}</a>
</div>
@endsection
