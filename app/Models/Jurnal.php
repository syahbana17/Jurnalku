<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $fillable = [
        'user_id', 'tanggal', 'hari', 'materi', 'kelas', 'metode',
        'kendala', 'evaluasi', 'matkul_s2', 'tugas_s2', 'insight',
    ];
    protected $casts = ['tanggal' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
}
