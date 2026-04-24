@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <section class="admin-header">
        <div>
            <h2 class="seccion-titulo">Usuarios</h2>
            <p class="admin-subtitle">Gestioná administradores, editores, roles y contraseñas.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">Nuevo usuario</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
        </div>
    </section>

    @if(session('ok'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(@json(session('ok')), 'success');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast(@json(session('error')), 'error');
            });
        </script>
    @endif

    @if($usuarios->count() === 0)
        <div class="admin-list-item">
            <div>
                <h3>No hay usuarios cargados</h3>
                <p>Creá el primer usuario desde el botón superior.</p>
            </div>
        </div>
    @endif

    <div class="admin-list">
        @foreach($usuarios as $usuario)
            <article class="admin-list-item">
                <div>
                    <h3>{{ $usuario->name }}</h3>

                    <div class="meta-noticia">
                        <span>{{ $usuario->email }}</span>

                        <span class="badge-rol {{ $usuario->rol }}">
                            {{ ucfirst($usuario->rol) }}
                        </span>
                        @if($usuario->id === auth()->id())
                            <span class="badge-categoria">Tu usuario</span>
                        @endif
                    </div>
                </div>

                <div class="admin-actions">
                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-secondary">
                        Editar
                    </a>

                    <form action="{{ route('admin.usuarios.resetPassword', $usuario) }}" method="POST" class="form-reset-password">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            Reset
                        </button>
                    </form>

                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" class="form-eliminar-usuario">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            Eliminar
                        </button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>

    @if($usuarios->hasPages())
        <div class="paginacion">
            {{ $usuarios->links('vendor.pagination.custom') }}
        </div>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-usuarios.js') }}"></script>
@endpush