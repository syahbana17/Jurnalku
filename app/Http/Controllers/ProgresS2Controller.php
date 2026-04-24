<?php

namespace App\Http\Controllers;

use App\Models\ProgresS2;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgresS2Controller extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'label'       => 'required|string',
            'persen'      => 'required|integer|min:0|max:100',
            'warna'       => 'required|in:blue,green,yellow,purple',
            'semester_id' => 'nullable|exists:semesters,id',
        ]);
        $data['user_id'] = Auth::id();
        $data['urutan']  = ProgresS2::where('user_id', Auth::id())->max('urutan') + 1;

        // Kalau tidak ada semester_id, pakai semester aktif
        if (empty($data['semester_id'])) {
            $aktif = Semester::where('user_id', Auth::id())->where('aktif', true)->first();
            $data['semester_id'] = $aktif?->id;
        }

        ProgresS2::create($data);
        return redirect()->route('dashboard')->with('success', 'Progress berhasil ditambahkan!');
    }

    public function update(Request $request, ProgresS2 $progres)
    {
        abort_if($progres->user_id !== Auth::id(), 403);
        $data = $request->validate([
            'label'  => 'required|string',
            'persen' => 'required|integer|min:0|max:100',
            'warna'  => 'required|in:blue,green,yellow,purple',
        ]);
        $progres->update($data);
        return redirect()->route('dashboard')->with('success', 'Progress diperbarui!');
    }

    public function destroy(ProgresS2 $progres)
    {
        abort_if($progres->user_id !== Auth::id(), 403);
        $progres->delete();
        return redirect()->route('dashboard')->with('success', 'Progress dihapus.');
    }
}
