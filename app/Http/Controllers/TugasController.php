<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Tugas::where('user_id', Auth::id())->orderBy('deadline');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $tugas = $query->paginate(15)->withQueryString();
        return view('tugas', compact('tugas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'        => 'required|string',
            'kategori'    => 'required|in:Sekolah,S2,Pribadi',
            'deadline'    => 'required|date',
            'jam_mulai'   => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'status'      => 'required|in:Belum,Sedang Dikerjakan,Selesai',
        ]);

        $data['user_id'] = Auth::id();
        Tugas::create($data);

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function updateStatus(Tugas $tugas)
    {
        abort_if($tugas->user_id !== Auth::id(), 403);
        $cycle = ['Belum', 'Sedang Dikerjakan', 'Selesai'];
        $idx   = array_search($tugas->status, $cycle);
        $tugas->update(['status' => $cycle[($idx + 1) % 3]]);
        return redirect()->route('tugas.index')->with('success', 'Status: ' . $tugas->status);
    }

    public function destroy(Tugas $tugas)
    {
        abort_if($tugas->user_id !== Auth::id(), 403);
        $tugas->delete();
        return redirect()->route('tugas.index')->with('success', 'Tugas dihapus.');
    }
}
