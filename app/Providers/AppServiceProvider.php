<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Directiva @nonce para agregar el CSP nonce a cualquier <script @nonce>
        Blade::directive('nonce', function () {
            return '<?php echo isset($cspNonce) ? \'nonce="\' . e($cspNonce) . \'"\' : \'\'; ?>';
        });
    }
}
