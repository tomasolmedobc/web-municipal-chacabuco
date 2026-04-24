@extends('layouts.app')

@section('title', 'Configuración del sistema')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Configuración del sistema</h2>
            <p class="admin-subtitle">Administrá opciones generales del portal.</p>
        </div>

        <a href="{{ route('admin.perfil.edit') }}" class="btn btn-secondary">Volver</a>
    </section>

    <div class="admin-form-card">
        <h3 style="margin-top:0;">Próximamente</h3>
        <p class="admin-subtitle">
            Acá vamos a configurar portada, logo municipal, datos de contacto e imagen por defecto.
        </p>
    </div>
@endsection