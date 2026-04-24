<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(Request $request)
    {
        $topik = $request->query('topik', 'semua');
        $query = Materi::where('user_id', Auth::id())->latest();
        if ($topik !== 'semua') $query->where('topik', $topik);
        $materis = $query->get();
        return view('materi', compact('materis', 'topik'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'    => 'required|string',
            'topik'    => 'required|in:Biologi,Fisika,Kimia',
            'kelas'    => 'nullable|string',
            'catatan'  => 'nullable|string',
            'link_url' => 'nullable|url',
            'file_pdf' => 'nullable|file|mimes:pdf|max:10240', // max 10MB
        ]);

        $data['user_id'] = Auth::id();

        // Handle link URL
        if (!empty($data['link_url'])) {
            $data['link_type'] = Materi::detectLinkType($data['link_url']);
        }

        // Handle PDF upload
        if ($request->hasFile('file_pdf')) {
            $path = $request->file('file_pdf')->store('materi-pdf/' . Auth::id(), 'public');
            $data['file_pdf']  = $path;
            $data['link_type'] = $data['link_type'] ?? 'pdf';
        }

        Materi::create($data);
        return redirect()->route('materi.index')->with('success', 'Materi berhasil disimpan!');
    }

    public function show(Materi $materi)
    {
        abort_if($materi->user_id !== Auth::id(), 403);
        return view('materi-show', compact('materi'));
    }

    public function destroy(Materi $materi)
    {
        abort_if($materi->user_id !== Auth::id(), 403);

        // Hapus file PDF jika ada
        if ($materi->file_pdf) {
            Storage::disk('public')->delete($materi->file_pdf);
        }

        $materi->delete();
        return redirect()->route('materi.index')->with('success', 'Materi dihapus.');
    }
}
