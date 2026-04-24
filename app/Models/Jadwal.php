<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = ['user_id', 'jam', 'jam_selesai', 'judul', 'keterangan', 'kategori'];
    public function user() { return $this->belongsTo(User::class); }
}
