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
        'bio',
        'avatar',
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

    public function managerEvaluations()
    {
        return $this->hasMany(\App\Models\ManagerEvaluation::class, 'manager_id');
    }

    public function selfEvaluations()
    {
        return $this->hasMany(\App\Models\SelfEvaluation::class, 'employee_id');
    }

    public function dpiPlans()
    {
        return $this->hasMany(\App\Models\DpiPlan::class);
    }

    /**
     * Slugs com nível de acesso total (equivalente a administrador).
     */
    private const SLUGS_ADMIN = ['administrator', 'ceo'];

    /**
     * Slugs com acesso ao módulo de RH/DP.
     */
    private const SLUGS_RH = ['administrator', 'ceo', 'hr', 'hr_manager'];

    /**
     * Slugs considerados gerentes de equipe/departamento.
     */
    private const SLUGS_GERENTE = ['manager', 'hr_manager'];

    /**
     * Scope: exclui perfis administrativos das listas de seleção.
     * Usado em dropdowns de atribuição de tarefas, avaliações, feedback, etc.
     */
    public function scopeNotAdmin($query)
    {
        return $query->whereHas('accessProfile', fn ($q) => $q->whereNotIn('slug', self::SLUGS_ADMIN));
    }

    /**
     * Verifica se o usuário tem acesso total (Administrador ou CEO).
     */
    public function isAdmin(): bool
    {
        return in_array($this->accessProfile?->slug, self::SLUGS_ADMIN);
    }

    /**
     * Verifica se o usuário tem acesso elevado ao módulo de RH/DP.
     * Inclui: CEO, Administrador, RH e Gerente de RH.
     */
    public function isRhOuDp(): bool
    {
        return in_array($this->accessProfile?->slug, self::SLUGS_RH);
    }

    /**
     * Verifica se o usuário é Gerente de Departamento ou Gerente de RH.
     */
    public function isGerente(): bool
    {
        return in_array($this->accessProfile?->slug, self::SLUGS_GERENTE);
    }

    /**
     * Verifica se o usuário é CEO.
     */
    public function isCeo(): bool
    {
        return $this->accessProfile?->slug === 'ceo';
    }

    /**
     * Verifica se o usuário é Gerente de RH.
     */
    public function isGerenteRh(): bool
    {
        return $this->accessProfile?->slug === 'hr_manager';
    }

    /**
     * Verifica se o usuário é um colaborador (sem perfil de gestão).
     */
    public function isEmployee(): bool
    {
        return $this->accessProfile?->slug === 'employee';
    }

    /**
     * Verifica se o usuário pode gerenciar pesquisas
     * (RH, Admin, CEO, Gerente de RH ou Gerente de departamento).
     */
    public function podeGerenciarPesquisas(): bool
    {
        return $this->isRhOuDp() || $this->isGerente();
    }

    public function podeAvaliar(): bool
    {
        return true;
    }

    // ── Feed social ───────────────────────────────────────────────────

    public function following(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id')
                    ->withTimestamps();
    }

    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id')
                    ->withTimestamps();
    }

    public function feedPosts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeedPost::class);
    }

    public function feedBookmarks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeedPostBookmark::class);
    }

    public function isFollowing(int $userId): bool
    {
        return $this->following()->where('following_id', $userId)->exists();
    }

    public function desligamentos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\RhDesligamento::class, 'user_id');
    }

    // ── Avatar helpers ────────────────────────────────────────────────

    /**
     * URL pública do avatar, ou null se não houver.
     */
    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    /**
     * Iniciais do nome (máx. 2 letras).
     */
    public function initials(): string
    {
        $parts = array_filter(explode(' ', trim($this->name)));
        if (count($parts) === 1) {
            return mb_strtoupper(mb_substr($parts[0], 0, 2));
        }
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
    }

    /**
     * Cor de fundo determinística baseada no nome.
     * Retorna uma classe Tailwind de gradiente.
     */
    public function avatarColor(): string
    {
        $colors = [
            'from-blue-500 to-indigo-600',
            'from-violet-500 to-purple-600',
            'from-teal-500 to-cyan-600',
            'from-orange-500 to-amber-500',
            'from-rose-500 to-pink-600',
            'from-green-500 to-emerald-600',
            'from-sky-500 to-blue-600',
            'from-fuchsia-500 to-pink-600',
        ];

        $index = array_sum(array_map('ord', str_split($this->name))) % count($colors);
        return $colors[$index];
    }
}
