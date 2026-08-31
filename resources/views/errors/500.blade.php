<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del servidor — Municipalidad de Chacabuco</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f6f8;
            color: #1a1a2e;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 24px;
        }
        header a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        header .sitename { font-size: 1.1rem; font-weight: 700; color: #1a1a2e; }
        header .subtitle { font-size: 0.8rem; color: #64748b; }
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 60px 48px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
        }
        .code {
            font-size: 5rem;
            font-weight: 800;
            color: #fee2e2;
            line-height: 1;
            margin-bottom: 8px;
        }
        h1 { font-size: 1.5rem; margin-bottom: 12px; }
        p { color: #64748b; line-height: 1.6; margin-bottom: 32px; }
        .btn {
            display: inline-block;
            background: #1a1a2e;
            color: #fff;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        footer {
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <header>
        <a href="/">
            <div>
                <div class="sitename">Municipalidad de Chacabuco</div>
                <div class="subtitle">Portal Oficial</div>
            </div>
        </a>
    </header>

    <main>
        <div class="card">
            <div class="code">500</div>
            <h1>Error interno del servidor</h1>
            <p>Ocurrió un problema inesperado.<br>Estamos trabajando para resolverlo. Intentá de nuevo en unos minutos.</p>
            <a href="/" class="btn">Ir al inicio</a>
        </div>
    </main>

    <footer>
        &copy; {{ date('Y') }} Municipalidad de Chacabuco — Todos los derechos reservados
    </footer>
</body>
</html>
