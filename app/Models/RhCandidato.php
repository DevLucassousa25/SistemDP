<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class RhCandidato extends Model
{
    protected $table = 'rh_candidatos';

    protected $fillable = ['nome', 'cpf', 'email', 'senha', 'telefone', 'token'];

    protected $hidden = ['senha', 'token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime'];
    }

    public function curriculos()
    {
        return $this->hasMany(RhCurriculo::class, 'candidato_id');
    }

    public function setSenhaAttribute($v)
    {
        $this->attributes['senha'] = Hash::make($v);
    }
}
