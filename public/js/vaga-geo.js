document.addEventListener('alpine:init', () => {
    Alpine.data('vagaGeo', ($wire) => ({
        geoLoading: false,
        geoError: '',
        geoBlocked: false,
        geoHint: (function() {
            const ua = navigator.userAgent;
            if (/Edg\//.test(ua)) {
                return 'No <strong>Edge</strong>: clique no ícone 🔒 na barra de endereço → <strong>Permissões para este site</strong> → <strong>Localização: Permitir</strong> → recarregue e tente novamente.';
            } else if (/Firefox\//.test(ua)) {
                return 'No <strong>Firefox</strong>: clique no ícone 🔒 → <strong>Limpar cookies e dados do site</strong> <em>ou</em> vá em <strong>Configurações → Privacidade → Permissões → Localização</strong> → remova este site da lista de bloqueados.';
            } else {
                return 'No <strong>Chrome</strong>: clique no ícone de <strong>localização bloqueada 📍</strong> na barra de endereço → selecione <strong>"Sempre permitir"</strong> → clique em <strong>Concluído</strong> e tente novamente.';
            }
        })(),
        stateMap: {
            'Acre':'AC','Alagoas':'AL','Amapá':'AP','Amazonas':'AM','Bahia':'BA',
            'Ceará':'CE','Distrito Federal':'DF','Espírito Santo':'ES','Goiás':'GO',
            'Maranhão':'MA','Mato Grosso':'MT','Mato Grosso do Sul':'MS',
            'Minas Gerais':'MG','Pará':'PA','Paraíba':'PB','Paraná':'PR',
            'Pernambuco':'PE','Piauí':'PI','Rio de Janeiro':'RJ',
            'Rio Grande do Norte':'RN','Rio Grande do Sul':'RS',
            'Rondônia':'RO','Roraima':'RR','Santa Catarina':'SC',
            'São Paulo':'SP','Sergipe':'SE','Tocantins':'TO'
        },
        async getLocation() {
            if (!navigator.geolocation) {
                this.geoError = 'Geolocalização não suportada neste browser.';
                return;
            }

            if (navigator.permissions) {
                try {
                    const perm = await navigator.permissions.query({ name: 'geolocation' });
                    if (perm.state === 'denied') {
                        this.geoBlocked = true;
                        this.geoError = 'blocked';
                        return;
                    }
                } catch (e) { /* Permissions API não suportada — tenta mesmo assim */ }
            }

            this.geoLoading = true;
            this.geoBlocked = false;
            this.geoError = '';

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    try {
                        const url = 'https://nominatim.openstreetmap.org/reverse'
                            + '?lat=' + pos.coords.latitude
                            + '&lon=' + pos.coords.longitude
                            + '&format=json&accept-language=pt-BR';
                        const res  = await fetch(url, { headers: { 'Accept-Language': 'pt-BR' } });
                        const json = await res.json();
                        const addr = json.address || {};
                        const city = addr.city || addr.town || addr.village || addr.municipality || '';
                        const stateRaw = addr.state || '';
                        const uf = this.stateMap[stateRaw] || stateRaw.substring(0, 2).toUpperCase();
                        await $wire.set('vCidade', city);
                        await $wire.set('vEstado', uf);
                        this.geoError = '';
                    } catch (e) {
                        this.geoError = 'fetch_error';
                    } finally {
                        this.geoLoading = false;
                    }
                },
                (err) => {
                    if (err.code === 1) {
                        this.geoBlocked = true;
                        this.geoError = 'blocked';
                    } else {
                        this.geoError = 'timeout';
                    }
                    this.geoLoading = false;
                },
                { timeout: 10000 }
            );
        }
    }));
});
