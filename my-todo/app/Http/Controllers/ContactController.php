<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:50',
            'message' => 'required|string|max:998',
        ]);

        Log::debug(print_r($validated, true));

        return response()->json([
            'status' => 'success',
            'message' => 'Your inquiry has been (virtually) sent successfully!',
        ]);
    }
}
