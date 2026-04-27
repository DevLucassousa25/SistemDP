# Guia de Segurança — Componentes Livewire

> **Todo novo componente Livewire DEVE estender `App\Livewire\SecureComponent`**  
> e seguir este checklist antes do merge/deploy.

---

## Checklist obrigatório por método público

Para cada método público que **recebe input do usuário ou modifica dados**, valide:

| # | Camada | O que verificar | Helper disponível |
|---|--------|-----------------|-------------------|
| 1 | **Autenticação** | O usuário está logado? | `$this->requireAuth()` |
| 2 | **Autorização por perfil** | Ele tem o perfil certo para esta ação? | `$this->requireRhOrAdmin()` / `$this->requireAdmin()` / `$this->requireRole([...])` |
| 3 | **IDOR** | O recurso pertence a ele ou ele é RH/Admin? | `$this->requireOwnerOrRhAdmin($ownerId)` |
| 4 | **#[Locked]** | Propriedades server-side têm `#[Locked]`? | Atributo Livewire `#[Locked]` |
| 5 | **Validação** | Todos os inputs passam por `$this->validate()`? | `$this->validate($rules)` |
| 6 | **Sanitização** | Campos de texto livre passam por `sanitize()`? | `$this->sanitize($value)` / `$this->sanitizeFields($data, [...])` |
| 7 | **Rate Limit** | Operações sensíveis têm limite de tentativas? | `$this->rateLimit('acao', max: 5, decay: 60)` |
| 8 | **Dupla checagem** | Auth é verificada na ABERTURA **e** na EXECUÇÃO? | Repita o check no método de confirmação |

---

## Template de novo componente

```php
<?php

namespace App\Livewire\Pages\MinhaArea;

use App\Livewire\SecureComponent;
use App\Models\MinhaEntidade;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;

class MeuComponente extends SecureComponent
{
    // ── [4] Propriedades server-side têm #[Locked] ────────────────────────────
    #[Locked]
    public ?int $entidadeId = null;

    // Propriedades de formulário (aceitas do cliente — sem #[Locked])
    public string $nome = '';
    public string $descricao = '';

    // ── Abertura ──────────────────────────────────────────────────────────────
    public function abrirModal(int $id): void
    {
        // [1] Autenticação (garantida pelo middleware 'auth' na rota)
        // [2] Autorização
        $this->requireRhOrAdmin();

        // [3] IDOR: verifica se o recurso pertence ao usuário ou ele tem acesso elevado
        $entidade = MinhaEntidade::findOrFail($id);
        $this->requireOwnerOrRhAdmin($entidade->user_id);

        $this->entidadeId = $id;
        $this->nome = $entidade->nome;
    }

    // ── Salvar ────────────────────────────────────────────────────────────────
    public function salvar(): void
    {
        // [7] Rate limit: evita abuso
        if (! $this->rateLimit('salvar-entidade', maxAttempts: 10, decaySeconds: 60)) {
            return;
        }

        // [8] Dupla checagem de autorização (defesa em profundidade)
        $this->requireRhOrAdmin();

        // [5] Validação de todos os inputs
        $this->validate([
            'nome'      => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
        ]);

        // [6] Sanitização de campos de texto livre
        $nome      = $this->sanitize($this->nome);
        $descricao = $this->sanitize($this->descricao);

        // [3] IDOR: re-busca o modelo pelo ID travado (#[Locked])
        $entidade = MinhaEntidade::findOrFail($this->entidadeId);
        $this->requireOwnerOrRhAdmin($entidade->user_id);

        $entidade->update([
            'nome'      => $nome,
            'descricao' => $descricao,
        ]);

        $this->clearRateLimit('salvar-entidade');
        $this->alertSuccess('Salvo com sucesso!');
        $this->dispatch('entidadeAtualizada');
    }
}
```

---

## Regras de propriedades Livewire

| Tipo de propriedade | Deve ter `#[Locked]`? |
|---------------------|-----------------------|
| IDs de recursos (ex: `$userId`, `$roomId`) | ✅ Sim |
| Modo de operação (ex: `$mode = 'create'`) | ✅ Sim |
| Flags de permissão (ex: `$allowMultiple`) | ✅ Sim |
| Listas carregadas server-side (`$departments`) | ✅ Sim |
| Campos de formulário que o usuário digita | ❌ Não (precisam ser livres) |
| Flags de UI (ex: `$open`, `$painelAberto`) | ⚠️ Opcional (depende do impacto) |

---

## Regras de validação recomendadas por tipo de campo

```php
// Nome / texto curto
'name'     => 'required|string|max:255',

// E-mail
'email'    => 'required|email:rfc,dns|max:255|unique:users,email',

// Senha (novos usuários)
'password' => ['required', Password::min(8)->letters()->numbers()],

// Texto longo (descrição, observação)
'descricao' => 'nullable|string|max:3000',

// Hora
'hora_inicio' => 'required|date_format:H:i',
'hora_fim'    => 'required|date_format:H:i|after:hora_inicio',

// Data
'data' => 'required|date|after_or_equal:today',

// Enum / select
'categoria' => 'required|in:sugestao,reclamacao,elogio,denuncia',

// Arquivo
'foto' => 'nullable|image|mimes:jpeg,png,webp|max:5120',

// ID de relacionamento
'department_id' => 'nullable|exists:departments,id',
```

---

## Resumo dos helpers disponíveis em SecureComponent

```php
// Autenticação
$this->requireAuth();

// Autorização por perfil
$this->requireAdmin();
$this->requireRhOrAdmin();
$this->requireRole(['administrator', 'hr', 'manager']);

// IDOR
$this->requireOwnerOrRhAdmin($ownerId);
$this->authorizePolicy('update', $model);

// Rate Limiting
$ok = $this->rateLimit('nome-da-acao', maxAttempts: 5, decaySeconds: 60);
$this->clearRateLimit('nome-da-acao');

// Sanitização XSS
$limpo  = $this->sanitize($string);
$dados  = $this->sanitizeFields($array, ['name', 'position']);

// Alertas padronizados
$this->alertSuccess('Título', 'Descrição opcional');
$this->alertError('Título', 'Descrição opcional');
$this->alertInfo('Título', 'Descrição opcional');
```
