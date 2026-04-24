<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['user_id', 'nama', 'aktif'];

    public function user()    { return $this->belongsTo(User::class); }
    public function progres() { return $this->hasMany(ProgresS2::class); }
}
