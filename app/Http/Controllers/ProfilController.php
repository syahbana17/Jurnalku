<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'whatsapp' => 'nullable|string|max:20|regex:/^[0-9+]+$/',
        ], [
            'whatsapp.regex' => 'Nomor WA hanya boleh angka dan tanda +',
        ]);

        Auth::user()->update($data);

        return redirect()->back()->with('success', 'Nomor WhatsApp berhasil disimpan!');
    }
}
