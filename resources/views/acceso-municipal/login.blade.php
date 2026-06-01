@extends('layouts.app')

@section('title', 'Acceso Municipal')

@section('content')
<section class="acceso-municipal-auth">
    <div class="acceso-municipal-auth__intro">
        <span class="section-badge">Área restringida</span>
        <h1>Acceso Municipal</h1>
        <p>
            Ingresá la clave de acceso para ver los enlaces internos del municipio.
        </p>
    </div>

    <form action="{{ route('acceso-municipal.authenticate') }}" method="POST" class="acceso-municipal-form">
        @csrf

        <label for="password">Clave de acceso</label>

        <input
            type="password"
            name="password"
            id="password"
            autocomplete="current-password"
            required
            autofocus
        >

        @error('password')
            <small class="auth-error">{{ $message }}</small>
        @enderror

        <button type="submit" class="btn btn-primary">
            Ingresar
        </button>
    </form>
</section>
@endsection
