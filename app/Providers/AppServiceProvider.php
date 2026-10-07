<?php

namespace App\Providers;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Proxies de confianza (IP/protocolo reales detrás de Cloudflare o un balanceador).
        // Se lee de config (no env) para que funcione con config:cache.
        $proxies = config('app.trusted_proxies', []);

        if ($proxies !== []) {
            TrustProxies::at(in_array('*', $proxies, true) ? '*' : $proxies);
        }

        // Directiva @nonce para agregar el CSP nonce a cualquier <script @nonce>
        Blade::directive('nonce', function () {
            return '<?php echo isset($cspNonce) ? \'nonce="\' . e($cspNonce) . \'"\' : \'\'; ?>';
        });
    }
}
