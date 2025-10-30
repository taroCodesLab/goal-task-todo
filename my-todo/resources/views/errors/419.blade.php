
@extends('layouts.app')

@section('title', 'Session Expired')

@section('content')
<div class="error-page text-center py-16">
    <h1 class="text-6xl font-bold mb-4">419</h1>
    <p class="text-lg mb-8">{{ __('messages.error_419') }}</p>
    <a href="{{ url()->previous() }}" class="btn btn-primary">{{ __('messages.go_back') }}</a>
</div>
@endsection
