<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Jurnal;
use App\Models\Materi;
use App\Models\Note;
use App\Models\ProgresS2;
use App\Models\Semester;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $uid = Auth::id();

        // Semester
        $semesters      = Semester::where('user_id', $uid)->orderBy('id')->get();
        $semesterAktif  = $semesters->firstWhere('aktif', true) ?? $semesters->last();

        // Kalau belum ada semester, buat default
        if ($semesters->isEmpty()) {
            $semesterAktif = Semester::create(['user_id' => $uid, 'nama' => 'Semester 2', 'aktif' => true]);
            $semesters = collect([$semesterAktif]);
        }

        // Filter semester dari request
        $semesterId = $request->query('semester', $semesterAktif?->id);
        $semesterView = $semesters->firstWhere('id', $semesterId) ?? $semesterAktif;

        $stats = [
            'jurnal' => Jurnal::where('user_id', $uid)->count(),
            'tugas'  => Tugas::where('user_id', $uid)->count(),
            'materi' => Materi::where('user_id', $uid)->count(),
        ];

        $tugasTerdekat = Tugas::where('user_id', $uid)
            ->where('status', '!=', 'Selesai')
            ->orderBy('deadline')->take(4)->get();

        $jadwals = Jadwal::where('user_id', $uid)->orderBy('jam')->get();

        // Progress berdasarkan semester yang dipilih
        $progres = ProgresS2::where('user_id', $uid)
            ->where('semester_id', $semesterView?->id)
            ->orderBy('urutan')->get();

        $quickNote = Note::where('user_id', $uid)->first();

        // Jadwal untuk kalender (semua)
        $jadwalKalender = Jadwal::where('user_id', $uid)->get()
            ->map(fn($j) => [
                'title' => $j->judul,
                'start' => now()->format('Y-m-d') . 'T' . str_pad($j->jam, 5, '0', STR_PAD_LEFT) . ':00',
                'end'   => $j->jam_selesai ? now()->format('Y-m-d') . 'T' . $j->jam_selesai . ':00' : null,
                'color' => match($j->kategori) { 'Sekolah' => '#3b82f6', 'S2' => '#10b981', 'Mandiri' => '#f59e0b', default => '#8b5cf6' },
                'extendedProps' => ['kategori' => $j->kategori, 'keterangan' => $j->keterangan],
            ])->toArray();

        // Tugas untuk kalender
        $tugasKalender = Tugas::where('user_id', $uid)->whereNotNull('deadline')->get()
            ->map(fn($t) => [
                'title' => '📋 ' . $t->nama,
                'start' => $t->deadline->format('Y-m-d'),
                'color' => match($t->status) { 'Selesai' => '#10b981', 'Sedang Dikerjakan' => '#f59e0b', default => '#ef4444' },
                'extendedProps' => ['kategori' => $t->kategori, 'status' => $t->status],
            ])->toArray();

        $kalenderEvents = array_merge($jadwalKalender, $tugasKalender);

        return view('dashboard', compact(
            'stats', 'tugasTerdekat', 'jadwals', 'progres',
            'quickNote', 'semesters', 'semesterView', 'kalenderEvents'
        ));
    }

    public function saveNote(Request $request)
    {
        $uid  = Auth::id();
        $note = Note::firstOrNew(['user_id' => $uid]);
        $note->user_id = $uid;
        $note->content = $request->input('content');
        $note->save();
        return response()->json(['ok' => true]);
    }
}
