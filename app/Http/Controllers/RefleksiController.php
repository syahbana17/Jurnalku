<?php

namespace App\Http\Controllers;

use App\Models\Refleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefleksiController extends Controller
{
    public function index()
    {
        $riwayat = Refleksi::where('user_id', Auth::id())->latest()->take(5)->get();
        return view('refleksi', compact('riwayat'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'berhasil'  => 'required|string',
            'gagal'     => 'required|string',
            'perbaikan' => 'required|string',
            'target'    => 'required|string',
        ]);
        $data['user_id'] = Auth::id();
        $data['tanggal'] = now()->toDateString();
        Refleksi::create($data);
        return redirect()->route('refleksi.index')->with('success', 'Refleksi berhasil disimpan!');
    }

    public function destroy(Refleksi $refleksi)
    {
        abort_if($refleksi->user_id !== Auth::id(), 403);
        $refleksi->delete();
        return redirect()->route('refleksi.index')->with('success', 'Refleksi dihapus.');
    }
}
