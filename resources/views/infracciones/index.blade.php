@extends('layouts.app')

@section('title', 'Consulta de Infracciones')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Inicio',              'url' => route('home')],
    ['label' => 'Trámites y Servicios','url' => route('tramites-servicios.index')],
    ['label' => 'Infracciones'],
]" />
<section class="inf-hero">
    <span class="section-badge">Tramites</span>
    <h1>Consulta de Infracciones</h1>
    <p>
        Consulte infracciones vigentes por dominio del vehiculo o por DNI. La busqueda muestra registros activos de los ultimos cinco anios.
    </p>
</section>

<section class="inf-layout">
    <form action="{{ route('infracciones.consultar') }}" method="POST" class="inf-form">
        @csrf

        <div class="inf-form__header">
            <span class="inf-form__icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>

            <div>
                <h2>Datos de consulta</h2>
                <p>Seleccione si desea buscar por dominio o por DNI.</p>
            </div>
        </div>

        <div class="inf-options">
            <label>
                <input type="radio" name="criterio" value="dominio" {{ old('criterio', $criterio) === 'dominio' ? 'checked' : '' }}>
                <span>Dominio</span>
            </label>

            <label>
                <input type="radio" name="criterio" value="dni" {{ old('criterio', $criterio) === 'dni' ? 'checked' : '' }}>
                <span>DNI</span>
            </label>
        </div>

        <label class="inf-field">
            <span>Dato a consultar</span>
            <input type="text"
                   name="valor"
                   value="{{ old('valor', $valor) }}"
                   placeholder="Ingrese dominio o DNI"
                   required>
            @error('valor')
                <small>{{ $message }}</small>
            @enderror
        </label>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i>
            Consultar
        </button>
    </form>

    <aside class="inf-help">
        <h2>Informacion util</h2>
        <p>
            Para dominio, ingrese la patente sin espacios. Para DNI, ingrese solo numeros.
        </p>

        <div class="inf-help__item">
            <i class="fa-solid fa-circle-info"></i>
            <span>Esta consulta replica el criterio del sistema anterior: infracciones activas de origen municipal.</span>
        </div>
    </aside>
</section>

@if($mensaje)
    <section class="inf-message">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ $mensaje }}</span>
    </section>
@endif

@if($consultado && ! $mensaje)
    <section class="inf-results">
        <div class="section-heading">
            <h2>Resultado de la consulta</h2>
            <p>
                Busqueda por {{ $criterio === 'dni' ? 'DNI' : 'dominio' }}: <strong>{{ $valor }}</strong>
            </p>
        </div>

        @if($infracciones->count())
            <div class="inf-table-wrap">
                <table class="inf-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Vehiculo</th>
                            <th>Infractor</th>
                            <th>Lugar</th>
                            <th>Causa</th>
                            <th>Acta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($infracciones as $infraccion)
                            <tr>
                                <td>{{ $infraccion->fecha }}</td>
                                <td>{{ $infraccion->patente ?: $infraccion->vehiculo }}</td>
                                <td>{{ $infraccion->nombre_completo ?: '-' }}</td>
                                <td>{{ trim(($infraccion->calle ?: '') . ' ' . ($infraccion->altura ?: '')) ?: '-' }}</td>
                                <td>{{ $infraccion->causa ?: '-' }}</td>
                                <td>{{ $infraccion->acta ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="inf-empty">
                No se encontraron infracciones para la consulta ingresada.
            </div>

            @if($criterio === 'dni')
                <div class="inf-free-debt">
                    <div>
                        <h3>Solicitar Libre de Deuda</h3>
                        <p>Complete nombre y apellido para emitir la constancia en formato PDF.</p>
                    </div>

                    <form action="{{ route('infracciones.libre-deuda') }}" method="POST" target="_blank" class="inf-free-debt__form">
                        @csrf
                        <input type="hidden" name="dni" value="{{ $valor }}">

                        <label>
                            <span>Apellido</span>
                            <input type="text" name="apellido" value="{{ old('apellido') }}" required>
                            @error('apellido') <small>{{ $message }}</small> @enderror
                        </label>

                        <label>
                            <span>Nombre</span>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre') <small>{{ $message }}</small> @enderror
                        </label>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-file-pdf"></i>
                            Emitir constancia
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </section>
@endif

@if($consultado && ! $mensaje && $criterio === 'dni' && $cedulas->count())
    <section class="inf-results">
        <div class="section-heading">
            <h2>Cedulas disponibles</h2>
            <p>Documentacion asociada al DNI consultado.</p>
        </div>

        <div class="inf-table-wrap">
            <table class="inf-table">
                <thead>
                    <tr>
                        <th>Fecha infraccion</th>
                        <th>Notificacion</th>
                        <th>Causa</th>
                        <th>Acta</th>
                        <th>Archivo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cedulas as $cedula)
                        <tr>
                            <td>{{ $cedula->fecha ?: '-' }}</td>
                            <td>{{ $cedula->notificacion ?: '-' }}</td>
                            <td>{{ $cedula->causa ?: '-' }}</td>
                            <td>{{ $cedula->acta ?: '-' }}</td>
                            <td>
                                <a href="{{ route('infracciones.cedula', $cedula->id_falta) }}" class="btn btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa-solid fa-download"></i>
                                    Descargar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
@endsection
