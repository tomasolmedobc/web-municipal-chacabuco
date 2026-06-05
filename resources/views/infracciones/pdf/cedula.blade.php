<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 36px 48px; }
        body { font-family: "DejaVu Sans", sans-serif; color: #111827; font-size: 12px; line-height: 1.55; }
        .header { position: relative; text-align: center; margin-bottom: 22px; }
        .logo { position: absolute; left: 0; top: 0; width: 58px; }
        h1 { margin: 0; font-size: 22px; letter-spacing: .3px; }
        h2 { margin: 4px 0 0; font-size: 15px; font-weight: 400; }
        h3 { margin: 18px 0 0; font-size: 16px; text-align: center; }
        .datos { margin: 28px 0 22px; }
        .datos p { margin: 0 0 5px; }
        .texto p { margin: 0 0 12px; text-align: justify; }
        .destacado { margin: 18px 0; text-align: center; font-weight: 700; text-decoration: underline; }
        .fecha { margin-top: 18px; text-align: right; }
        .nota { margin-top: 18px; font-size: 10px; color: #374151; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $logoPath }}" class="logo" alt="">
        <h1>MUNICIPALIDAD DE CHACABUCO</h1>
        <h2>Juzgado Municipal de Faltas</h2>
        <h3>CEDULA</h3>
    </div>

    <div class="datos">
        <p><strong>Señor/a:</strong> {{ $cedula->nombre_completo ?: '-' }}</p>
        <p><strong>DNI:</strong> {{ $cedula->dni ? number_format((int) $cedula->dni, 0, ',', '.') : '-' }}</p>
        <p><strong>Domicilio:</strong> {{ $cedula->domicilio ?: '-' }}</p>
        <p><strong>Ciudad:</strong> {{ $cedula->localidad ?: '-' }}</p>
        <p><strong>Causa N°:</strong> {{ $cedula->causa ?: '-' }} &nbsp;&nbsp; <strong>Acta N°:</strong> {{ $cedula->acta ?: '-' }}</p>
    </div>

    <div class="texto">
        @if((int) $cedula->id_disposicion === 2)
            <p>El Señor Juez de Faltas, a cargo del Juzgado Municipal de Faltas del Partido de Chacabuco, le notifica que con motivo del acta contravencional referenciada usted podrá acogerse al beneficio del pago voluntario, abonando el porcentaje previsto por la normativa vigente dentro del plazo establecido a partir de notificada la presente.</p>
            <p>Vencido dicho plazo, y para el supuesto de no haber optado por dicho beneficio, queda emplazado a concurrir a este Tribunal, sito en calle San Martín N° 81, primer piso, en el horario de {{ $cedula->horario ?: '-' }} horas, a efectos de ejercer su defensa y ofrecer la prueba que intente valerse.</p>
            <div class="destacado">QUEDA USTED DEBIDAMENTE NOTIFICADO E INTIMADO</div>
        @else
            <p>El Señor Juez de Faltas del Partido de Chacabuco emplaza y notifica a usted a concurrir a este Tribunal, sito en calle San Martín N° 81, primer piso, el día {{ $cedula->fecha_cedula ?: '-' }} en el horario de {{ $cedula->horario ?: '-' }} horas, con motivo de la causa referenciada, para ejercer su defensa y ofrecer la prueba que intente valerse.</p>
            <p>Se deja constancia que la comparecencia deberá ser personal o por apoderado y que el procedimiento es oral, conforme la normativa aplicable.</p>
            <div class="destacado">QUEDA USTED DEBIDAMENTE NOTIFICADO</div>
        @endif
    </div>

    <div class="fecha">Chacabuco, {{ now()->format('d/m/Y') }}</div>
    <div class="nota">Consultas: Municipalidad de Chacabuco - Juzgado Municipal de Faltas.</div>
</body>
</html>
