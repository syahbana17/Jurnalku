<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jurnal::where('user_id', Auth::id())->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($qb) use ($q) {
                $qb->where('materi', 'like', "%$q%")
                   ->orWhere('kelas', 'like', "%$q%")
                   ->orWhere('insight', 'like', "%$q%");
            });
        }

        $jurnals = $query->paginate(10)->withQueryString();
        return view('jurnal', compact('jurnals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'   => 'required|date',
            'hari'      => 'required|string',
            'materi'    => 'required|string',
            'kelas'     => 'required|string',
            'metode'    => 'nullable|string',
            'kendala'   => 'nullable|string',
            'evaluasi'  => 'nullable|string',
            'matkul_s2' => 'nullable|string',
            'tugas_s2'  => 'nullable|string',
            'insight'   => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();
        Jurnal::create($data);

        return redirect()->route('jurnal.index')->with('success', 'Jurnal berhasil disimpan!');
    }

    public function destroy(Jurnal $jurnal)
    {
        abort_if($jurnal->user_id !== Auth::id(), 403);
        $jurnal->delete();
        return redirect()->route('jurnal.index')->with('success', 'Jurnal dihapus.');
    }
}
