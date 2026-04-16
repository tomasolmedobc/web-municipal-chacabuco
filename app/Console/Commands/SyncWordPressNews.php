<?php

namespace App\Console\Commands;

use App\Models\Noticia;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncWordPressNews extends Command
{
    protected $signature = 'wordpress:sync-news
        {--since= : Sync only posts modified on or after this date/time}
        {--limit= : Max number of posts to process}
        {--status=* : Override WordPress post statuses to sync}
        {--dry-run : Preview changes without writing to the local database}
        {--force : Update rows even if wp_modified_at did not change}';

    protected $description = 'Synchronize news posts from a WordPress database into the local noticias table';

    public function handle(): int
    {
        $connection = config('wordpress.connection', 'wordpress');
        $prefix = config('wordpress.table_prefix', 'wp_');
        $statuses = $this->option('status');
        $statuses = count($statuses) > 0 ? $statuses : config('wordpress.post_statuses', ['publish']);
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        try {
            $since = $this->option('since') ? Carbon::parse((string) $this->option('since')) : null;
        } catch (\Throwable $exception) {
            $this->error('La opción --since no tiene un formato de fecha válido.');

            return self::FAILURE;
        }

        try {
            DB::connection($connection)->getPdo();
        } catch (\Throwable $exception) {
            $this->error("No se pudo conectar a WordPress usando la conexión [{$connection}].");
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $postsTable = "{$prefix}posts";
        $postMetaTable = "{$prefix}postmeta";
        $usersTable = "{$prefix}users";

        $query = DB::connection($connection)
            ->table("{$postsTable} as posts")
            ->leftJoin("{$usersTable} as users", 'users.ID', '=', 'posts.post_author')
            ->leftJoin("{$postMetaTable} as featured_meta", function (JoinClause $join) {
                $join->on('featured_meta.post_id', '=', 'posts.ID')
                    ->where('featured_meta.meta_key', '=', '_thumbnail_id');
            })
            ->leftJoin("{$postsTable} as attachments", 'attachments.ID', '=', 'featured_meta.meta_value')
            ->where('posts.post_type', config('wordpress.post_type', 'post'))
            ->whereIn('posts.post_status', $statuses)
            ->orderBy('posts.ID')
            ->select([
                'posts.ID as wp_id',
                'posts.post_title as titulo',
                'posts.post_content as contenido',
                'posts.post_date as fecha',
                'posts.post_name as slug',
                'posts.post_author as autor_id',
                'posts.post_modified as wp_modified_at',
                'users.display_name as autor_nombre',
                'attachments.guid as imagen_destacada',
            ]);

        if ($since instanceof Carbon) {
            $query->where('posts.post_modified', '>=', $since->format('Y-m-d H:i:s'));
        }

        $totalSourceRows = (clone $query)->count('posts.ID');

        if ($totalSourceRows === 0) {
            $this->warn('No se encontraron noticias para sincronizar con los filtros actuales.');

            return self::SUCCESS;
        }

        $this->info("Noticias encontradas en WordPress: {$totalSourceRows}");
        $this->line('Conexión origen: '.$connection);
        $this->line('Estados: '.implode(', ', $statuses));
        if ($since instanceof Carbon) {
            $this->line('Desde: '.$since->format('Y-m-d H:i:s'));
        }
        if ($dryRun) {
            $this->comment('Modo simulación activado: no se guardarán cambios.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $processed = 0;
        $stop = false;

        $query->chunk(200, function ($rows) use (&$created, &$updated, &$skipped, &$processed, &$stop, $dryRun, $force, $limit) {
            $existing = Noticia::query()
                ->whereIn('wp_id', $rows->pluck('wp_id')->all())
                ->get()
                ->keyBy('wp_id');

            foreach ($rows as $row) {
                if ($limit !== null && $processed >= $limit) {
                    $stop = true;

                    return false;
                }

                $payload = $this->mapWordPressRow($row);
                $current = $existing->get($payload['wp_id']);
                $shouldSkip = $current !== null
                    && !$force
                    && $current->wp_modified_at !== null
                    && $payload['wp_modified_at'] !== null
                    && $current->wp_modified_at->equalTo($payload['wp_modified_at']);

                if ($shouldSkip) {
                    $skipped++;
                    $processed++;
                    continue;
                }

                if ($dryRun) {
                    $current ? $updated++ : $created++;
                    $processed++;
                    continue;
                }

                $data = $payload;
                $wpId = $data['wp_id'];
                unset($data['wp_id']);

                Noticia::query()->updateOrCreate(
                    ['wp_id' => $wpId],
                    $data
                );

                $current ? $updated++ : $created++;
                $processed++;
            }
        });

        $this->newLine();
        $this->table(
            ['Procesadas', 'Creadas', 'Actualizadas', 'Sin cambios'],
            [[
                $processed,
                $created,
                $updated,
                $skipped,
            ]]
        );

        if ($stop) {
            $this->comment('Se alcanzó el límite solicitado.');
        }

        return self::SUCCESS;
    }

    protected function mapWordPressRow(object $row): array
    {
        $titulo = trim((string) ($row->titulo ?? ''));
        $slug = trim((string) ($row->slug ?? ''));
        $autor = $row->autor_nombre ?: $row->autor_id;

        return [
            'wp_id' => (int) $row->wp_id,
            'titulo' => $titulo !== '' ? $titulo : 'Sin título',
            'contenido' => $this->normalizeContent((string) ($row->contenido ?? '')),
            'fecha' => $row->fecha ?: now(),
            'autor' => $autor !== null ? (string) $autor : null,
            'slug' => $this->normalizeSlug($slug, $titulo, (int) $row->wp_id),
            'imagen_destacada' => $this->normalizeMediaValue($row->imagen_destacada),
            'wp_modified_at' => filled($row->wp_modified_at) ? Carbon::parse($row->wp_modified_at) : null,
        ];
    }

    protected function normalizeSlug(string $slug, string $titulo, int $wpId): string
    {
        $base = $slug !== '' ? $slug : Str::slug($titulo);

        return $base !== '' ? $base : "noticia-{$wpId}";
    }

    protected function normalizeContent(string $content): string
    {
        return $this->normalizeMediaValue($content) ?? $content;
    }

    protected function normalizeMediaValue(?string $value): ?string
    {
        if (blank($value)) {
            return $value;
        }

        $sourceUploadsPath = '/'.trim((string) config('wordpress.media.source_uploads_path', '/wp-content/uploads'), '/');
        $targetBaseUrl = rtrim((string) config('wordpress.media.target_base_url', '/images'), '/');
        $targetSuffix = (string) config('wordpress.media.target_suffix', '_resultado');
        $targetExtension = ltrim((string) config('wordpress.media.target_extension', 'webp'), '.');

        $pattern = '#(?:(?:https?://[^"\'\s)]+)?'.preg_quote($sourceUploadsPath, '#').'/)([^"\'\s)?]+?)\.(?:jpe?g|png|gif|webp)(\?[^"\'\s)]*)?#i';

        return preg_replace_callback($pattern, function (array $matches) use ($targetBaseUrl, $targetSuffix, $targetExtension) {
            $relativePath = str_replace('\\', '/', $matches[1]);
            $directory = trim(pathinfo($relativePath, PATHINFO_DIRNAME), './');
            $filename = pathinfo($relativePath, PATHINFO_FILENAME);

            $rebuilt = $filename.$targetSuffix.'.'.$targetExtension;

            if ($directory !== '' && $directory !== '.') {
                $rebuilt = $directory.'/'.$rebuilt;
            }

            return $targetBaseUrl.'/'.$rebuilt;
        }, $value);
    }
}
