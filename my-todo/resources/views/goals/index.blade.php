
@extends('layouts.app')

@section('title', 'Goalリスト')

@section('content')
@if(session('error'))
<p>{{ session('error') }}</p>
@elseif(session('success'))
<p>{{ session('success') }}</p>
@endif
@guest
    <p class="text-red-500">※登録やログインをしていない場合、Goal は最大 3 件、Task は最大 5 件まで追加可能です。ログインすると、他の端末からでも同じ内容の追加や操作が可能になります。</p>
@endguest
<div class="container mx-auto flex items-center justify-center">
    <h1 class="my-4 font-bold border-y-2 border-blue-400">Goalリスト</h1>
</div>
<div class="grid grid-cols-1 gap-4 my-4">
    <!-- GOALリスト表示部分 -->
    <div id="app">
        <goal-list :initial-goals='@json($goals)' :is-authenticated='@json(Auth::check())' class="flex justify-center"></goal-list>
    </div>
</div>

@endsection