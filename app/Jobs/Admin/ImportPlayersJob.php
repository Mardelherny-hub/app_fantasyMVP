<?php

namespace App\Jobs\Admin;

use App\Services\Admin\PlayerImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Pathinfo;
use Maatwebsite\Excel\Facades\Excel;


class ImportPlayersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

    public function __construct(
        public int $initiatorUserId,
        public string $storedPath,
        public string $mode = 'upsert',   // create|upsert|update
        public ?string $idColumn = 'id',  // usado en update/upsert
    ) {}

    public function handle(PlayerImportService $service): void
    {
        $full = Storage::path($this->storedPath);
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));

        // 1) Cargar a arreglo de filas
        $rows = match ($ext) {
            'csv', 'txt' => $this->loadCsv($full),
            'xlsx'       => $this->loadXlsxFallback($full), // opcional si instalamos paquete
            default      => throw new \RuntimeException("Extensión no soportada: .$ext"),
        };

        // 2) Importar
        $summary = $service->import($rows, $this->mode, $this->idColumn);

        Log::info('Players import finished', ['summary' => $summary, 'by_user' => $this->initiatorUserId]);
        // Podés notificar por mail/notification aquí si querés.
    }

    /** CSV simple (sin dependencias). */
    private function loadCsv(string $path): array
    {
        $fh = fopen($path, 'r');
        if (!$fh) throw new \RuntimeException("No se pudo abrir el CSV");

        $header = null;
        $rows = [];
        while (($data = fgetcsv($fh, 0, ',')) !== false) {
            if ($header === null) {
                $header = array_map(fn($h) => trim(strtolower($h)), $data);
                continue;
            }
            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = $data[$i] ?? null;
            }
            $rows[] = $row;
        }
        fclose($fh);
        return $rows;
    }

    private function loadXlsxFallback(string $path): array
    {
        $rows = [];
        $sheets = \Maatwebsite\Excel\Facades\Excel::toCollection(null, $path);
        if ($sheets->isEmpty()) {
            return [];
        }

        $sheet = $sheets->first();
        $header = [];
        foreach ($sheet as $i => $row) {
            // Convertir a array plano
            $row = $row->toArray();
            if ($i === 0) {
                // Cabeceras normalizadas
                $header = array_map(fn($h) => trim(strtolower($h)), $row);
                continue;
            }
            $assoc = [];
            foreach ($header as $idx => $key) {
                $assoc[$key] = $row[$idx] ?? null;
            }
            $rows[] = $assoc;
        }
        return $rows;
    }

}
// Si instalás maatwebsite/excel, podés usar algo así:
/*
    private function loadXlsx(string $path): array
    {
        return \Maatwebsite\Excel\Facades\Excel::toArray([], $path)[0] ?? [];
    }
*/

// Nota: si el CSV es muy grande, podés procesarlo por chunks en vez de cargar todo en memoria. 

