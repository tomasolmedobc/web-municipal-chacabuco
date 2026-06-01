@extends('layouts.app')

@section('title', 'Consulta de Expedientes')

@section('content')
<section class="exp-hero">
    <span class="section-badge">Tramites</span>
    <h1>Consulta de Expedientes</h1>
    <p>
        Ingrese los datos del expediente para consultar su estado, detalle y recorrido dentro del municipio.
    </p>
</section>

<section class="exp-layout">
    <form action="{{ route('expedientes.consultar') }}" method="POST" class="exp-form">
        @csrf

        <div class="exp-form__header">
            <span class="exp-form__icon">
                <i class="fa-solid fa-folder-open"></i>
            </span>

            <div>
                <h2>Datos del expediente</h2>
                <p>La letra es opcional. Complete numero y año para realizar la busqueda.</p>
            </div>
        </div>

        <div class="exp-form__grid">
            <label>
                <span>Numero</span>
                <input type="number"
                       name="numero"
                       value="{{ old('numero', $form['numero']) }}"
                       min="1"
                       placeholder="Ej: 1234"
                       required>
                @error('numero')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label>
                <span>Letra</span>
                <input type="text"
                       name="letra"
                       value="{{ old('letra', $form['letra']) }}"
                       maxlength="3"
                       placeholder="Ej: A">
                @error('letra')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label>
                <span>Año</span>
                <input type="number"
                       name="anio"
                       value="{{ old('anio', $form['anio']) }}"
                       min="2000"
                       max="2100"
                       required>
                @error('anio')
                    <small>{{ $message }}</small>
                @enderror
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i>
            Consultar expediente
        </button>
    </form>

    <aside class="exp-help">
        <h2>Como buscar</h2>
        <p>
            Use el numero y el año del expediente. Si el expediente incluye letra, agreguela en el campo correspondiente.
        </p>

        <div class="exp-help__item">
            <i class="fa-solid fa-circle-info"></i>
            <span>Solo se muestran expedientes con visibilidad publica.</span>
        </div>
    </aside>
</section>

@if($mensaje)
    <section class="exp-message">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ $mensaje }}</span>
    </section>
@endif

@if($expediente)
    <section class="exp-result">
        <div class="exp-result__header">
            <div>
                <span class="section-badge">Resultado</span>
                <h2>Detalle del tramite</h2>
            </div>

            <span class="exp-status">
                {{ \App\Http\Controllers\ExpedienteConsultaController::estado($expediente->Estado) }}
            </span>
        </div>

        <div class="exp-detail">
            <div>
                <span>Expediente</span>
                <strong>{{ $form['numero'] }} {{ $form['letra'] }} / {{ $form['anio'] }}</strong>
            </div>

            <div>
                <span>Fecha y hora de ingreso</span>
                <strong>{{ \App\Http\Controllers\ExpedienteConsultaController::fecha($expediente->FechaHoraIngreso) }}</strong>
            </div>

            @if($expediente->Descripcion)
                <div>
                    <span>Descripcion</span>
                    <strong>{{ $expediente->Descripcion }}</strong>
                </div>
            @endif

            @if($expediente->Detalle)
                <div>
                    <span>Detalle</span>
                    <strong>{{ $expediente->Detalle }}</strong>
                </div>
            @endif

            @if($expediente->Nombres)
                <div>
                    <span>Nombre y apellido</span>
                    <strong>{{ trim($expediente->Nombres . ' ' . $expediente->Apellidos) }}</strong>
                </div>
            @endif

            @if($expediente->RazonSocial)
                <div>
                    <span>Razon social</span>
                    <strong>{{ $expediente->RazonSocial }}</strong>
                </div>
            @endif

            @if($expediente->MotivoAnulacion)
                <div>
                    <span>Motivo de anulacion</span>
                    <strong>{{ $expediente->MotivoAnulacion }}</strong>
                </div>
            @endif

            @if($expediente->ObservacionPub)
                <div>
                    <span>Observaciones</span>
                    <strong>{{ $expediente->ObservacionPub }}</strong>
                </div>
            @endif
        </div>
    </section>

    <section class="exp-steps">
        <div class="section-heading">
            <h2>Recorrido del expediente</h2>
            <p>Movimientos registrados para la consulta realizada.</p>
        </div>

        @forelse($pasos as $index => $paso)
            <article class="exp-step">
                <span class="exp-step__number">{{ $index + 1 }}</span>

                <div>
                    <h3>{{ $paso->Nombre ?: 'Oficina no informada' }}</h3>
                    <p>
                        Ingreso el {{ \App\Http\Controllers\ExpedienteConsultaController::fecha($paso->FechaHoraIngreso) }}.
                    </p>

                    @if($paso->ObservacionPub)
                        <p class="exp-step__note">{{ $paso->ObservacionPub }}</p>
                    @endif
                </div>
            </article>
        @empty
            <div class="exp-message">
                <i class="fa-solid fa-circle-info"></i>
                <span>No se encontraron pases para este expediente.</span>
            </div>
        @endforelse
    </section>
@endif
@endsection
