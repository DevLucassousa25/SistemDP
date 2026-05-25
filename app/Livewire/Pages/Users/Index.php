<?php

namespace App\Livewire\Pages\Users;

use App\Livewire\SecureComponent;
use App\Models\ConfiguracaoEmpresa;

class Index extends SecureComponent
{
    public string $aba = 'usuarios'; // usuarios | configuracoes

    // ── Configurações da empresa ──────────────────────────────────────
    public string $cfgNomeEmpresa  = '';
    public string $cfgEmailDominio = '';
    public string $cfgCnpj         = '';
    public string $cfgTelefone     = '';
    public string $cfgSite         = '';

    public function mount(): void
    {
        $this->requireRhOrAdmin();

        $cfg = ConfiguracaoEmpresa::instancia();
        $this->cfgNomeEmpresa  = $cfg->nome_empresa  ?? '';
        $this->cfgEmailDominio = $cfg->email_dominio ?? '';
        $this->cfgCnpj         = $cfg->cnpj          ?? '';
        $this->cfgTelefone     = $cfg->telefone       ?? '';
        $this->cfgSite         = $cfg->site           ?? '';
    }

    public function openModal(): void
    {
        $this->requireRhOrAdmin();
        $this->dispatch('openUserModal');
    }

    public function salvarConfiguracoes(): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'cfgEmailDominio' => 'nullable|regex:/^[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}$/',
            'cfgSite'         => 'nullable|url',
        ], [
            'cfgEmailDominio.regex' => 'Informe apenas o domínio, sem @ ou http. Ex: empresa.com.br',
            'cfgSite.url'           => 'Informe uma URL válida. Ex: https://empresa.com.br',
        ]);

        // Limpa o domínio: remove http(s):// e @ se o usuário digitou errado
        $dominio = trim($this->cfgEmailDominio);
        $dominio = preg_replace('#^https?://#', '', $dominio);
        $dominio = ltrim($dominio, '@');
        $dominio = strtolower($dominio);

        ConfiguracaoEmpresa::instancia()->update([
            'nome_empresa'  => $this->cfgNomeEmpresa  ?: null,
            'email_dominio' => $dominio               ?: null,
            'cnpj'          => $this->cfgCnpj         ?: null,
            'telefone'      => $this->cfgTelefone      ?: null,
            'site'          => $this->cfgSite          ?: null,
        ]);

        $this->cfgEmailDominio = $dominio;

        session()->flash('cfg_sucesso', 'Configurações salvas com sucesso!');
    }

    public function render()
    {
        return view('livewire.pages.users.index');
    }
}
