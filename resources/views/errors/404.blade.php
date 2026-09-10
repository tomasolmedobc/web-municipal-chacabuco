<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada — Municipalidad de Chacabuco</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style nonce="{{ $cspNonce ?? '' }}">
        .error-page {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
        }
        .error-card {
            max-width: 560px;
            width: 100%;
            text-align: center;
        }
        .error-code {
            font-size: 96px;
            font-weight: 800;
            line-height: 1;
            color: var(--primary);
            opacity: .15;
            margin-bottom: 0;
            letter-spacing: -4px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            margin-top: -8px;
        }
        .error-desc {
            color: var(--muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .error-search {
            display: flex;
            gap: 8px;
            margin-bottom: 40px;
        }
        .error-search-wrap {
            position: relative;
            flex: 1;
        }
        .error-search-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 14px;
            pointer-events: none;
        }
        .error-search-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            background: var(--card);
            color: var(--text);
            font-size: 15px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .error-search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }
        .error-links {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            text-align: left;
        }
        .error-link {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            text-decoration: none;
            color: var(--text);
            font-size: 13px;
            font-weight: 600;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }
        .error-link:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
            transform: translateY(-1px);
        }
        .error-link i {
            color: var(--primary);
            font-size: 15px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }
        .error-links-titulo {
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        @include('partials.header')

        <main>
            <div class="error-page">
                <div class="error-card">
                    <div class="error-code">404</div>
                    <h1 class="error-title">Página no encontrada</h1>
                    <p class="error-desc">
                        La página que buscás no existe o fue movida.<br>
                        Probá buscando lo que necesitás:
                    </p>

                    <form action="/buscar" method="GET" class="error-search" role="search">
                        <div class="error-search-wrap">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input
                                type="search"
                                name="q"
                                class="error-search-input"
                                placeholder="Buscá noticias, trámites, turismo…"
                                autocomplete="off"
                                minlength="3"
                                autofocus
                                aria-label="Buscar en el portal"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>

                    <p class="error-links-titulo">Accesos rápidos</p>
                    <div class="error-links">
                        <a href="/" class="error-link">
                            <i class="fa-solid fa-house"></i> Inicio
                        </a>
                        <a href="/noticias" class="error-link">
                            <i class="fa-solid fa-newspaper"></i> Noticias
                        </a>
                        <a href="/tramites-y-servicios" class="error-link">
                            <i class="fa-solid fa-file-lines"></i> Trámites
                        </a>
                        <a href="/gobierno-abierto" class="error-link">
                            <i class="fa-solid fa-landmark"></i> Gobierno Abierto
                        </a>
                        <a href="/turismo" class="error-link">
                            <i class="fa-solid fa-map-location-dot"></i> Turismo
                        </a>
                        <a href="/telefonos-utiles" class="error-link">
                            <i class="fa-solid fa-phone"></i> Teléfonos Útiles
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <footer class="site-footer">
            <div class="site-footer__bottom">
                © {{ date('Y') }} Municipalidad de Chacabuco — Todos los derechos reservados
            </div>
        </footer>
    </div>
</body>
</html>
