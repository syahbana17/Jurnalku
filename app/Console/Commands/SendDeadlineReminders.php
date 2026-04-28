<?php

namespace App\Console\Commands;

use App\Mail\DeadlineReminder;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDeadlineReminders extends Command
{
    protected $signature   = 'reminder:deadline';
    protected $description = 'Kirim email reminder untuk tugas yang deadline besok (H-1)';

    public function handle(): void
    {
        $besok = now()->addDay()->toDateString();

        // Ambil semua user yang punya tugas deadline besok
        $users = User::whereHas('tugas', function ($q) use ($besok) {
            $q->whereDate('deadline', $besok)
              ->where('status', '!=', 'Selesai');
        })->get();

        if ($users->isEmpty()) {
            $this->info('Tidak ada tugas deadline besok.');
            return;
        }

        foreach ($users as $user) {
            $tugasBesok = Tugas::where('user_id', $user->id)
                ->whereDate('deadline', $besok)
                ->where('status', '!=', 'Selesai')
                ->get()
                ->map(fn($t) => [
                    'nama'     => $t->nama,
                    'kategori' => $t->kategori,
                    'deadline' => $t->deadline->translatedFormat('d F Y'),
                    'status'   => $t->status,
                ])->toArray();

            Mail::to($user->email)->send(
                new DeadlineReminder($user->name, $tugasBesok)
            );

            $this->info("Email terkirim ke: {$user->email} ({$user->name}) — " . count($tugasBesok) . " tugas");
        }

        $this->info('Selesai mengirim reminder.');
    }
}
