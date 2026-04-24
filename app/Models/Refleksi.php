<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refleksi extends Model
{
    protected $fillable = ['user_id', 'tanggal', 'berhasil', 'gagal', 'perbaikan', 'target'];
    protected $casts    = ['tanggal' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
}
