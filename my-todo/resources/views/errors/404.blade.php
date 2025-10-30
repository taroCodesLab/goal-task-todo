@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="error-page text-center py-16">
    <h1 class="text-6xl font-bold mb-4">404</h1>
    <p class="text-lg mb-8">{{ __('messages.error_404') }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary">{{ __('messages.back_to_home') }}</a>
</div>
@endsection
