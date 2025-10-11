<?php

namespace App\Http\Controllers\Admin\Imports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Imports\StorePlayersImportRequest;
use App\Services\Admin\PlayerImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Throwable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class PlayersImportController extends Controller
{
    public function index(Request $request)
    {
        Log::info('PlayersImportController@index hit', [
            'user_id' => optional($request->user())->id,
            'ip'      => $request->ip(),
            'route'   => $request->path(),
            'locale'  => app()->getLocale(),
        ]);

        return view('admin.players.import');
    }

    public function store(StorePlayersImportRequest $request, PlayerImportService $service)
    {
        // Detectar si el CSV tiene la columna clave indicada
        $hasKeyInRows = !empty($rows) && array_key_exists($request->input('id_column'), $rows[0]);

        // Si pidieron upsert/update pero NO existe la columna clave en el CSV, convertir a create
         $mode = $request->string('mode')->toString();

        $t0 = microtime(true);

        // Entrada al método
        Log::info('PlayersImport@store START', [
            'user_id'   => optional($request->user())->id,
            'ip'        => $request->ip(),
            'route'     => $request->path(),
            'locale'    => app()->getLocale(),
            'has_file'  => $request->hasFile('file'),
            'mode'      => $request->input('mode'),
            'id_column' => $request->input('id_column'),
            'filename'  => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : null,
            'mime'      => $request->hasFile('file') ? $request->file('file')->getClientMimeType() : null,
            'size'      => $request->hasFile('file') ? $request->file('file')->getSize() : null,
        ]);

        try {
            // Guardamos el archivo (también sirve para inspección si hace falta)
            $stored = $request->file('file')->store('imports/players');
            $full   = Storage::path($stored);
            $ext    = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            $size   = @filesize($full);

            Log::info('PlayersImport file stored', [
                'stored_path'   => $stored,
                'absolute_path' => $full,
                'extension'     => $ext,
                'size_bytes'    => $size,
            ]);

            // Parseo según extensión
            $tParse0 = microtime(true);
            $rows = match ($ext) {
                'csv', 'txt' => $this->loadCsv($full),
                'xlsx'       => $this->loadXlsx($full),
                default      => throw new \RuntimeException("Extensión no soportada: .$ext"),
            };
            $tParse1 = microtime(true);

            Log::info('PlayersImport parsed', [
                'rows_count'   => is_array($rows) ? count($rows) : null,
                'parse_time_s' => round($tParse1 - $tParse0, 4),
                'header_guess' => $this->guessHeaderFromRows($rows),
                'first_row'    => $this->firstRowPreview($rows),
            ]);

            // Import inmediato
            $tImp0 = microtime(true);
            $summary = $service->import(
                $rows,
                $mode,
                $request->string('id_column')->toString()
            );
            $tImp1 = microtime(true);

            Log::info('PlayersImport FINISHED', [
                'summary'       => $summary,
                'import_time_s' => round($tImp1 - $tImp0, 4),
                'total_time_s'  => round(microtime(true) - $t0, 4),
            ]);

            return back()->with([
                'status'  => '✅ Importación realizada.',
                'summary' => $summary,
            ]);
        } catch (Throwable $e) {
            Log::error('PlayersImport ERROR', [
                'message'      => $e->getMessage(),
                'code'         => $e->getCode(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
                'trace_top'    => collect(explode("\n", $e->getTraceAsString()))->take(8)->all(),
                'total_time_s' => round(microtime(true) - $t0, 4),
            ]);

            return back()
                ->withErrors(['file' => 'Error al importar: '.$e->getMessage()])
                ->withInput();
        }
    }

    /** CSV simple (encabezado en la primera fila). */
    private function loadCsv(string $path): array
    {
        Log::info('PlayersImport loadCsv()', ['path' => $path]);

        $fh = fopen($path, 'r');
        if (!$fh) {
            throw new \RuntimeException("No se pudo abrir el CSV: $path");
        }

        $header = null;
        $rows   = [];
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

        Log::info('PlayersImport loadCsv() done', ['rows_count' => count($rows)]);
        return $rows;
    }

    /** XLSX con Maatwebsite\Excel. */
    private function loadXlsx(string $path): array
    {
        Log::info('PlayersImport loadXlsx()', ['path' => $path]);

        $rows   = [];
        $sheets = Excel::toCollection(null, $path);
        if ($sheets->isEmpty()) {
            Log::info('PlayersImport loadXlsx() empty sheets');
            return [];
        }

        $sheet  = $sheets->first();
        $header = [];
        foreach ($sheet as $i => $row) {
            $row = $row->toArray();
            if ($i === 0) {
                $header = array_map(fn($h) => trim(strtolower($h)), $row);
                continue;
            }
            $assoc = [];
            foreach ($header as $idx => $key) {
                $assoc[$key] = $row[$idx] ?? null;
            }
            $rows[] = $assoc;
        }

        Log::info('PlayersImport loadXlsx() done', ['rows_count' => count($rows)]);
        return $rows;
    }

    /** Descargar plantilla XLSX (Maatwebsite\Excel) */
    public function template(Request $request)
    {
        // XLSX usando Maatwebsite\Excel
        $headers = [
            'full_name','known_as','position','nationality','birthdate',
            'height_cm','weight_kg','photo_url','is_active',            
        ];

        $data = [
            $headers, // fila de encabezado
            // fila ejemplo
            ['Aitor Sánchez','Aitor','GK','ES','1995-05-18','191','89','https://i.imgur.com/placeholder.jpg','1'],
        ];

        // Export simple sin clase externa
        $collection = collect($data);
        return \Maatwebsite\Excel\Facades\Excel::download(new class($collection) implements \Maatwebsite\Excel\Concerns\FromCollection {
            public function __construct(private \Illuminate\Support\Collection $rows) {}
            public function collection() { return $this->rows; }
        }, 'players_import_template.xlsx');
    }

    // (opcional) Versión CSV
    public function templateCsv(Request $request): StreamedResponse
    {
        $headers = [
            'full_name','known_as','position','nationality','birthdate',
            'height_cm','weight_kg','photo_url','is_active',
        ];

        $rows = [
            $headers,
            ['Aitor Sánchez','Aitor','GK','ES','1995-05-18','191','89','https://i.imgur.com/placeholder.jpg','1'],
        ];

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // Forzar UTF-8 con BOM para Excel en Windows (opcional)
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        }, 'players_import_template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }


    /** Helpers de log para no inundar el archivo */
    private function guessHeaderFromRows(array $rows): array
    {
        if (empty($rows)) return [];
        return array_keys($rows[0]);
    }

    private function firstRowPreview(array $rows): array
    {
        if (empty($rows)) return [];
        // mostrás solo hasta 10 claves para no llenar el log
        return array_slice($rows[0], 0, 10, true);
    }
}
