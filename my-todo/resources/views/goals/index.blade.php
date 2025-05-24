
@extends('layouts.app')

@section('title', 'Goalリスト')

@section('content')
@if(session('error'))
<p>{{ session('error') }}</p>
@elseif(session('success'))
<p>{{ session('success') }}</p>
@endif
<div class="container mx-auto flex items-center justify-center">
    <h1 class="my-4 font-bold border-y-2 border-blue-400">Goalリスト</h1>
</div>
<div class="grid grid-cols-1 gap-4 my-4">
    <form action="{{ route('todo.store') }}" method="post" class="flex justify-center">
        @csrf
        <div class="flex items-center space-x-2">
            <div class="mb-4">
                <label for="goal" class="block text-gray-700 text-sm font-bold mb-2">GOAL</label>
                <input type="text" name="goal" id="goal" value="{{ old('goal') }}" placeholder="GOAL" class="shadow appearance-none border rounded w-64 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded relative mt-3">
            <span class="text-2xl leading-none">+</span>
            </button>
        </div>
    </form>
    <!-- GOALリスト表示部分 -->
    <div id="app">
        <goal-list :initial-goals='@json($goals)' class="flex justify-center"></goal-list>
    </div>
</div>

@endsection