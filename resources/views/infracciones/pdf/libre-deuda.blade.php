<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 36px 48px; }
        body { font-family: "DejaVu Sans", sans-serif; color: #111827; font-size: 12px; line-height: 1.65; }
        .watermark { position: fixed; left: 70px; top: 285px; transform: rotate(-35deg); color: #f3b5b5; font-size: 34px; font-weight: 700; opacity: .45; }
        .watermark.dni { left: 210px; top: 340px; }
        .header { position: relative; text-align: center; margin-bottom: 44px; }
        .logo { position: absolute; left: 0; top: 0; width: 58px; }
        h1 { margin: 0; font-size: 22px; letter-spacing: .3px; }
        h2 { margin: 4px 0 0; font-size: 15px; font-weight: 400; }
        h3 { margin: 20px 0 0; font-size: 16px; text-align: center; }
        .contenido { margin-top: 42px; }
        .contenido p { margin: 0 0 16px; text-align: justify; }
    </style>
</head>
<body>
    <div class="watermark">{{ $nombreCompleto }}</div>
    <div class="watermark dni">{{ $data['dni'] }}</div>

    <div class="header">
        <img src="{{ $logoPath }}" class="logo" alt="">
        <h1>MUNICIPALIDAD DE CHACABUCO</h1>
        <h2>Juzgado Municipal de Faltas</h2>
        <h3>CONSTANCIA DE LIBRE DEUDA DE INFRACCIONES</h3>
    </div>

    <div class="contenido">
        <p>Juzgado de Faltas N° 1 de Chacabuco. Chacabuco, {{ now()->format('d/m/Y') }}. Por la presente certifico que el Sr./Sra. {{ $nombreCompleto }}, DNI {{ $data['dni'] }}, no registra deuda en este Tribunal en concepto de multas por infracciones.</p>
        <p>La presente constancia se emite de manera online y contiene los datos del interesado impresos en marca de agua. En esas condiciones, es válida para ser presentada ante el organismo o persona requirente por el término de 7 días.</p>
    </div>
</body>
</html>
