<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCurriculoTag extends Model
{
    protected $table = 'rh_curriculo_tags';

    protected $fillable = ['curriculo_id', 'tag'];

    public function curriculo()
    {
        return $this->belongsTo(RhCurriculo::class, 'curriculo_id');
    }
}
