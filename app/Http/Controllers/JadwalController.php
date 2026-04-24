<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'jam'         => 'required|string|max:10',
            'jam_selesai' => 'nullable|string|max:10',
            'judul'       => 'required|string',
            'keterangan'  => 'nullable|string',
            'kategori'    => 'required|in:Sekolah,S2,Mandiri,Lainnya',
        ]);
        $data['user_id'] = Auth::id();
        Jadwal::create($data);
        return redirect()->route('dashboard')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function destroy(Jadwal $jadwal)
    {
        abort_if($jadwal->user_id !== Auth::id(), 403);
        $jadwal->delete();
        return redirect()->route('dashboard')->with('success', 'Jadwal dihapus.');
    }
}
