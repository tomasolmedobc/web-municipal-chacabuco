@props(['error' => 'cf-turnstile-response'])

@if(config('services.turnstile.site_key') && config('services.turnstile.secret_key'))
    @once
        @push('scripts_head')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endonce

    <div class="turnstile-box" style="margin:16px 0;">
        <div class="cf-turnstile"
             data-sitekey="{{ config('services.turnstile.site_key') }}"
             data-theme="auto"
             data-language="es"></div>
        @error($error)
            <small class="auth-error" style="display:block;margin-top:6px;">{{ $message }}</small>
        @enderror
    </div>
@endif
