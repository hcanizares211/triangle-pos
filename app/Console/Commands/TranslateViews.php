<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TranslateViews extends Command
{
    protected $signature = 'app:translate-views {--dry-run : Muestra cambios sin aplicarlos}';
    protected $description = 'Envuelve los textos hardcodeados en archivos blade con __() para i18n';

    private array $translations = [];

    public function handle(): int
    {
        $jsonPath = resource_path('lang/es.json');
        if (!file_exists($jsonPath)) {
            $this->error("No existe resources/lang/es.json");
            return 1;
        }
        $this->translations = json_decode(file_get_contents($jsonPath), true) ?? [];

        // Ordenar por longitud descendente: reemplazar primero los strings más largos
        uksort($this->translations, fn($a, $b) => strlen($b) - strlen($a));

        $bladeFiles = $this->getBladeFiles();
        $this->info("Archivos blade encontrados: " . count($bladeFiles));

        $changed = 0;
        foreach ($bladeFiles as $file) {
            if ($this->processFile($file)) {
                $changed++;
            }
        }

        $this->newLine();
        $this->info("✅ Archivos modificados: $changed / " . count($bladeFiles));
        return 0;
    }

    private function getBladeFiles(): array
    {
        $paths = [resource_path('views'), base_path('Modules')];
        $files = [];

        foreach ($paths as $basePath) {
            if (!is_dir($basePath)) continue;
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
            foreach ($it as $file) {
                if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) continue;
                $path = $file->getPathname();
                // Saltar archivos vendor
                if (str_contains($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) continue;
                $files[] = $path;
            }
        }

        return $files;
    }

    private function processFile(string $path): bool
    {
        $content = file_get_contents($path);
        $original = $content;

        foreach ($this->translations as $key => $spanish) {
            $esc = preg_quote($key, '/');
            $call = "__('$key')";

            // Saltar si ya está traducido
            if (str_contains($content, $call)) continue;

            // 1. @section('title', 'KEY')
            $content = preg_replace(
                "/@section\('title',\s*'$esc'\)/",
                "@section('title', __('$key'))",
                $content
            );

            // 2. >KEY</a>  (breadcrumbs, links)
            $content = preg_replace(
                '/>' . $esc . '<\/a>/',
                ">{{ __('$key') }}</a>",
                $content
            );

            // 3. >KEY</li>  (breadcrumb activo, items de lista)
            $content = preg_replace(
                '/>' . $esc . '<\/li>/',
                ">{{ __('$key') }}</li>",
                $content
            );

            // 4. >KEY</th>  (cabeceras de tabla)
            $content = preg_replace(
                '/>' . $esc . '<\/th>/',
                ">{{ __('$key') }}</th>",
                $content
            );

            // 5. </i> KEY  al final de línea (menú lateral, botones con icono)
            $content = preg_replace(
                '/(><\/i>)\s+' . $esc . '\s*$/m',
                "$1 {{ __('$key') }}",
                $content
            );

            // 6. >KEY <i class="bi  (botón: texto antes de un icono Bootstrap)
            $content = preg_replace(
                '/>\s*' . $esc . '\s*<i\s+class="bi/',
                ">{{ __('$key') }} <i class=\"bi",
                $content
            );

            // 7. >KEY <span class="text-danger">  (label con asterisco)
            $content = preg_replace(
                '/>\s*' . $esc . '\s*<span\s+class="text-danger">/',
                ">{{ __('$key') }} <span class=\"text-danger\">",
                $content
            );

            // 8. >KEY</label>  (label sin asterisco)
            $content = preg_replace(
                '/>\s*' . $esc . '\s*<\/label>/',
                ">{{ __('$key') }}</label>",
                $content
            );

            // 9. <option value="KEY">KEY</option>  (opciones de select)
            $content = preg_replace(
                '/<option\s+value="' . $esc . '">' . $esc . '<\/option>/',
                "<option value=\"$key\">{{ __('$key') }}</option>",
                $content
            );

            // 10. font-weight-bold small">KEY</div>  (tarjetas del dashboard)
            $content = preg_replace(
                '/font-weight-bold small">\s*' . $esc . '\s*<\/div>/',
                "font-weight-bold small\">{{ __('$key') }}</div>",
                $content
            );

            // 11. >KEY</h5>, >KEY</h6>  (títulos de sección)
            $content = preg_replace(
                '/>' . $esc . '<\/(h[1-6])>/',
                ">{{ __('$key') }}</$1>",
                $content
            );

            // 12. >KEY</span>  (badges, spans de texto)
            $content = preg_replace(
                '/>\s*' . $esc . '\s*<\/span>(?!\s*\}})/',
                ">{{ __('$key') }}</span>",
                $content
            );
        }

        if ($content === $original) {
            return false;
        }

        if (!$this->option('dry-run')) {
            file_put_contents($path, $content);
        }

        $rel = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
        $this->line('  <fg=green>✔</> ' . $rel);
        return true;
    }
}
