<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
        protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'position',
        'access_profile_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\Task::class, 'assigned_to');
    }

    public function accessProfile()
    {
        return $this->belongsTo(AccessProfile::class);
    }

    /**
     * Verifica se o usuário é Administrador do sistema.
     */
    public function isAdmin(): bool
    {
        return $this->accessProfile?->slug === 'administrator';
    }

    /**
     * Verifica se o usuário é RH ou Administrador (acesso elevado).
     * Usado para controle de visibilidade de manifestações, usuários, etc.
     */
    public function isRhOuDp(): bool
    {
        return in_array($this->accessProfile?->slug, ['administrator', 'hr']);
    }

    /**
     * Verifica se o usuário é Gerente de Departamento.
     */
    public function isGerente(): bool
    {
        return $this->accessProfile?->slug === 'manager';
    }

    /**
     * Verifica se o usuário pode gerenciar pesquisas
     * (RH, Admin ou Gerente de departamento).
     */
    public function podeGerenciarPesquisas(): bool
    {
        return $this->isRhOuDp() || $this->isGerente();
    }
}
