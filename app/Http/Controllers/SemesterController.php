<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SemesterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['nama' => 'required|string|max:50']);
        $data['user_id'] = Auth::id();
        $data['aktif']   = false;
        Semester::create($data);
        return redirect()->route('dashboard')->with('success', 'Semester "' . $data['nama'] . '" ditambahkan!');
    }

    public function activate(Semester $semester)
    {
        abort_if($semester->user_id !== Auth::id(), 403);
        // Non-aktifkan semua semester user ini
        Semester::where('user_id', Auth::id())->update(['aktif' => false]);
        $semester->update(['aktif' => true]);
        return redirect()->route('dashboard')->with('success', $semester->nama . ' sekarang aktif.');
    }

    public function destroy(Semester $semester)
    {
        abort_if($semester->user_id !== Auth::id(), 403);
        $semester->delete();
        return redirect()->route('dashboard')->with('success', 'Semester dihapus.');
    }
}
