<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $fillable = ['user_id', 'nama', 'kategori', 'deadline', 'jam_mulai', 'jam_selesai', 'status'];
    protected $casts    = ['deadline' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
}
