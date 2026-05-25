<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCandidaturaComentario extends Model
{
    protected $table = 'rh_candidatura_comentarios';

    protected $fillable = ['candidatura_id', 'user_id', 'comentario'];

    public function candidatura()
    {
        return $this->belongsTo(RhCandidatura::class, 'candidatura_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
