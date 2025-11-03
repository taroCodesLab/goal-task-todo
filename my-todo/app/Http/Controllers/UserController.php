<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Not using
    public function edit()
    {
        // $user = Auth::user();
        // return view('users.edit', compact('user'));
    }

    // Not using
    public function update(Request $request)
    {
        // $user = Auth::user();

        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => [
        //         'required',
        //         'string',
        //         'email',
        //         'max:255',
        //         Rule::unique('users')->ignore($user->id),
        //     ],
        //     'password' => ['nullable', 'confirmed', 'min:8'],
        // ]);

        // $user->name = $request->name;
        // $user->email = $request->email;

        // if ($request->filled('password')) {
        //     $user->password = Hash::make($request->password);
        // }

        // $user->save();

        // return redirect()->route('users.edit')->with('status', 'ユーザー情報を更新しました');
    }
}
