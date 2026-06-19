<div class="evento-reminder" id="evento-reminder" aria-live="polite" aria-label="Recordatorio de eventos" hidden>
    <div class="evento-reminder__header">
        <div class="evento-reminder__header-left">
            <i class="fa-solid fa-calendar-days"></i>
            Eventos próximos
        </div>
        <button class="evento-reminder__close" id="evento-reminder-close" aria-label="Cerrar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="evento-reminder__body" id="evento-reminder-body">
        {{-- Poblado dinámicamente por JS --}}
    </div>

    <div class="evento-reminder__footer">
        <a href="{{ route('turismo.index') }}">
            Ver todos los eventos
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</div>
