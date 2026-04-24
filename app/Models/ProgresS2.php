<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresS2 extends Model
{
    protected $table    = 'progres_s2';
    protected $fillable = ['user_id', 'label', 'persen', 'warna', 'urutan', 'semester_id'];
    public function user()     { return $this->belongsTo(User::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
}
