<?php

namespace App\Services;

use App\Models\PublicAccessButton;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PublicAccessButtonService
{
    public const SECCION_TRAMITES = 'tramites_servicios.tramites';
    public const SECCION_SERVICIOS = 'tramites_servicios.servicios';
    public const SECCION_GOBIERNO_ABIERTO = 'gobierno_abierto';

    public function tramitesVisibles(): Collection
    {
        return $this->filtrarVisibles(
            self::SECCION_TRAMITES,
            $this->normalizarConfig(config('tramites_servicios.tramites', []))
        );
    }

    public function serviciosVisibles(): Collection
    {
        return $this->filtrarVisibles(
            self::SECCION_SERVICIOS,
            $this->normalizarConfig(config('tramites_servicios.servicios', []))
        );
    }

    public function gobiernoAbiertoVisibles(array $items): Collection
    {
        return $this->filtrarVisibles(
            self::SECCION_GOBIERNO_ABIERTO,
            $this->normalizarConfig($items)
        );
    }

    public function sincronizarTodo(array $gobiernoAbiertoItems): Collection
    {
        return collect([
            self::SECCION_TRAMITES => $this->sincronizarSeccion(
                self::SECCION_TRAMITES,
                $this->normalizarConfig(config('tramites_servicios.tramites', []))
            ),
            self::SECCION_SERVICIOS => $this->sincronizarSeccion(
                self::SECCION_SERVICIOS,
                $this->normalizarConfig(config('tramites_servicios.servicios', []))
            ),
            self::SECCION_GOBIERNO_ABIERTO => $this->sincronizarSeccion(
                self::SECCION_GOBIERNO_ABIERTO,
                $this->normalizarConfig($gobiernoAbiertoItems)
            ),
        ]);
    }

    public function sincronizarSeccion(string $seccion, Collection $items): Collection
    {
        $items->values()->each(function (array $item, int $index) use ($seccion) {
            $button = PublicAccessButton::firstOrNew([
                    'seccion' => $seccion,
                    'clave' => $item['clave'],
            ]);

            $button->fill([
                'titulo' => $item['titulo'],
                'descripcion' => $item['descripcion'] ?? null,
                'url' => $item['url'] ?? null,
                'icono' => $item['icono'] ?? null,
                'orden' => $index + 1,
            ])->save();
        });

        return PublicAccessButton::query()
            ->where('seccion', $seccion)
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get();
    }

    public function normalizarConfig(array $items): Collection
    {
        return collect($items)->map(function (array $item) {
            $item['clave'] = $item['clave'] ?? Str::slug($item['titulo'] ?? '');

            return $item;
        });
    }

    private function filtrarVisibles(string $seccion, Collection $items): Collection
    {
        $this->sincronizarSeccion($seccion, $items);

        $ocultos = PublicAccessButton::query()
            ->where('seccion', $seccion)
            ->where('activo', false)
            ->pluck('clave')
            ->all();

        $overrides = PublicAccessButton::query()
            ->where('seccion', $seccion)
            ->whereNotNull('url_personalizada')
            ->pluck('url_personalizada', 'clave');

        return $items
            ->reject(fn (array $item) => in_array($item['clave'], $ocultos, true))
            ->map(function (array $item) use ($overrides) {
                $url = trim((string) $overrides->get($item['clave'], ''));

                if ($url !== '') {
                    $item['url'] = $url;
                }

                return $item;
            })
            ->values();
    }
}
