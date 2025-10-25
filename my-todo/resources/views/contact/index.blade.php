@extends('layouts.app')

@section('title', 'お問い合わせ')

@section('content')
<div class="mac-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg my-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-700 text-center">お問い合わせフォーム</h2>
    {{-- フラッシュメッセージ（送信成功時など）--}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- バリデーションエラー --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contact.send') }}" method="post" class="space-y-6">
        @csrf

        {{-- 名前 --}}
        <div>
            <label for="name" class="block text-gray-700 font-semibold mb-1">お名前</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
        </div>

        {{-- メールアドレス --}}
        <div>
            <label for="email" class="block text-gray-700 font-semibold mb-1">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
        </div>

        {{-- 件名 --}}
        <div>
            <label for="subject" class="block text-gray-700 font-semibold mb-1">件名</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
        </div>

        {{-- 内容 --}}
        <div>
            <label for="message" class="block text-gray-700 font-semibold mb-1">お問い合わせ内容</label>
            <textarea name="message" id="message" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 foucus:coutline-none focus:ring-2 focus:ring-cyan-400" required>{{ old('message') }}</textarea>
        </div>

        {{-- 送信ボタン --}}
        <div class="text-center">
            <button type="submit" class="bg-[#4682b4] text-white font-bold px-6 py-2 rounded-lg hover:bg-[#5fa2cf] transition duration-200">
                送信する
            </button>
        </div>
    </form>
</div>
@endsection