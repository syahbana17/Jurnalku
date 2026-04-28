<?php

namespace App\Console\Commands;

use App\Models\Tugas;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendDeadlineReminders extends Command
{
    protected $signature   = 'reminder:deadline';
    protected $description = 'Kirim WA reminder untuk tugas deadline besok (H-1)';

    public function handle(): void
    {
        // Cek dulu apakah kolom whatsapp ada
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'whatsapp')) {
            $this->info('Kolom whatsapp belum ada, skip.');
            return;
        }

        $besok = now()->addDay()->toDateString();

        $users = User::whereNotNull('whatsapp')
            ->whereHas('tugas', function ($q) use ($besok) {
                $q->whereDate('deadline', $besok)
                  ->where('status', '!=', 'Selesai');
            })->get();

        if ($users->isEmpty()) {
            $this->info('Tidak ada reminder yang perlu dikirim.');
            return;
        }

        foreach ($users as $user) {
            $tugasBesok = Tugas::where('user_id', $user->id)
                ->whereDate('deadline', $besok)
                ->where('status', '!=', 'Selesai')
                ->get();

            $pesan = "⏰ *Reminder Deadline - Jurnalku*\n\n";
            $pesan .= "Halo *{$user->name}*! 👋\n\n";
            $pesan .= "Kamu punya *" . $tugasBesok->count() . " tugas* yang deadline-nya *besok*:\n\n";

            foreach ($tugasBesok as $i => $t) {
                $pesan .= ($i + 1) . ". *{$t->nama}*\n";
                $pesan .= "   📁 {$t->kategori} | 📅 {$t->deadline->translatedFormat('d F Y')}\n";
                $pesan .= "   Status: {$t->status}\n\n";
            }

            $pesan .= "Segera selesaikan ya! 💪\n";
            $pesan .= config('app.url') . "/tugas";

            $this->sendWhatsApp($user->whatsapp, $pesan);
            $this->info("WA terkirim ke: {$user->whatsapp} ({$user->name})");
        }

        $this->info('Selesai.');
    }

    private function sendWhatsApp(string $nomor, string $pesan): void
    {
        $token = config('services.fonnte.token');

        if (!$token) {
            $this->error('FONNTE_TOKEN belum diset!');
            return;
        }

        Http::withHeaders(['Authorization' => $token])
            ->post('https://api.fonnte.com/send', [
                'target'  => $nomor,
                'message' => $pesan,
            ]);
    }
}
