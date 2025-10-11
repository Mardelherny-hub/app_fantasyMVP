<?php

namespace App\Services\Admin;

use App\Models\Player;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PlayerImportService
{
    /** Requeridos SOLO para crear (no para update). Ajustá a tu esquema real. */
    private const REQUIRED_FOR_CREATE = ['position'];

    /** Mapeo de posición texto → código INT */
    private const POSITION_MAP = [
        'gk' => 1, 'df' => 2, 'mf' => 3, 'fw' => 4,
        'goalkeeper' => 1, 'defender' => 2, 'midfielder' => 3, 'forward' => 4,
        'arquero' => 1, 'portero' => 1, 'defensa' => 2, 'mediocampista' => 3, 'volante' => 3, 'delantero' => 4,
        'gardien' => 1, 'défenseur' => 2, 'milieu' => 3, 'attaquant' => 4,
    ];

    /** Estrategia de matching cuando NO hay id_column o no viene en la fila */
    private const MATCH_KEYS = [
        // 1) external_id
        ['external_id'],
        // 2) full_name + birthdate (recomendado)
        ['full_name','birthdate'],
        // 3) known_as + birthdate
        ['known_as','birthdate'],
        // 4) solo full_name (último recurso, descomentar si querés usarlo)
        ['full_name'],
    ];

    public function import(array $rows, string $mode = 'upsert', ?string $idColumn = 'id'): array
    {
        $table    = (new Player)->getTable();
        $columns  = Schema::getColumnListing($table);
        $fillable = (new Player)->getFillable();
        $allow    = $fillable ?: $columns;

        $created = $updated = $skipped = 0;
        $skippedNoAttrs = $skippedNoKey = $skippedMissingRequired = 0;
        $errors = [];
        $hits = ['id_column'=>0,'external_id'=>0,'full_name+birthdate'=>0,'known_as+birthdate'=>0,'full_name'=>0];

        foreach ($rows as $i => $row) {
            $row = $this->normalizeKeys($row);
            $row = $this->transformRow($row); // mapea position / castea / normaliza fechas

            $attrs = Arr::only($row, $allow);
            if (empty($attrs)) {
                $skipped++; $skippedNoAttrs++;
                $this->pushErr($errors, $i, 'Sin atributos válidos para Player');
                continue;
            }

            // Resolver si existe (para update/upsert)
            $existing = $this->findExisting($row, $idColumn, $hits);

            if ($mode === 'create') {
                // Validar requeridos de CREATE
                $missing = $this->missingRequired($attrs, self::REQUIRED_FOR_CREATE);
                if (!empty($missing)) {
                    $skipped++; $skippedMissingRequired++;
                    $this->pushErr($errors, $i, 'Faltan requeridos para crear: '.implode(',', $missing));
                    continue;
                }
                Player::create($attrs);
                $created++;
                continue;
            }

            if ($mode === 'update') {
                if ($existing) {
                    $existing->fill($attrs)->save();
                    $updated++;
                } else {
                    $skipped++; $skippedNoKey++;
                    $this->pushErr($errors, $i, "No se encontró registro para UPDATE (sin clave o sin match)");
                }
                continue;
            }

            // mode === 'upsert'
            if ($existing) {
                $existing->fill($attrs)->save();
                $updated++;
            } else {
                // Validar requeridos de CREATE
                $missing = $this->missingRequired($attrs, self::REQUIRED_FOR_CREATE);
                if (!empty($missing)) {
                    $skipped++; $skippedMissingRequired++;
                    $this->pushErr($errors, $i, 'Faltan requeridos para crear: '.implode(',', $missing));
                    continue;
                }
                Player::create($attrs);
                $created++;
            }
        }

        Log::info('PlayerImportService summary details', [
            'created' => $created, 'updated' => $updated, 'skipped' => $skipped,
            'skippedNoAttrs' => $skippedNoAttrs,
            'skippedNoKey' => $skippedNoKey,
            'skippedMissingRequired' => $skippedMissingRequired,
            'match_hits' => $hits,
            'errors_preview' => array_slice($errors, 0, 10),
        ]);

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => array_slice($errors, 0, 50),
        ];
    }

    private function normalizeKeys(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $k = trim(strtolower($k));
            $k = str_replace([' ', '-'], '_', $k);
            $out[$k] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }

    private function transformRow(array $row): array
    {
        // position → int
        if (array_key_exists('position', $row)) {
            $row['position'] = $this->mapPosition($row['position']);
        }

        // nationality a 2 letras (si te conviene)
        if (isset($row['nationality']) && is_string($row['nationality'])) {
            $row['nationality'] = strtoupper(substr(trim($row['nationality']), 0, 2));
        }

        foreach (['overall','age','height_cm','weight_kg','team_id'] as $intField) {
            if (isset($row[$intField])) $row[$intField] = $this->toInt($row[$intField]);
        }
        foreach (['price','value','market_value'] as $floatField) {
            if (isset($row[$floatField])) $row[$floatField] = $this->toFloat($row[$floatField]);
        }

        foreach (['birthdate','born_at','date_of_birth'] as $dateField) {
            if (isset($row[$dateField])) {
                $row[$dateField] = $this->toDate($row[$dateField]); // Y-m-d
            }
        }

        return $row;
    }

    private function findExisting(array $row, ?string $idColumn, array &$hits): ?\App\Models\Player
    {
        // normalizador rápido para strings
        $norm = function ($v) {
            return is_string($v) ? trim($v) : $v;
        };

        // 1) id_column explícita (id, external_id, etc.)
        if (!empty($idColumn) && array_key_exists($idColumn, $row)) {
            $val = $norm($row[$idColumn] ?? null);
            if ($val !== null && $val !== '') {
                $hits["id:$idColumn"] = ($hits["id:$idColumn"] ?? 0) + 1;
                return \App\Models\Player::where($idColumn, $val)->first();
            }
        }

        // 2) external_id
        if (!empty($row['external_id'])) {
            $val = $norm($row['external_id']);
            if ($val !== '') {
                $hits['external_id'] = ($hits['external_id'] ?? 0) + 1;
                if ($p = \App\Models\Player::where('external_id', $val)->first()) {
                    return $p;
                }
            }
        }

        // 3) full_name + birthdate
        if (!empty($row['full_name']) && !empty($row['birthdate'])) {
            $name = $norm($row['full_name']);
            $dob  = $norm($row['birthdate']); // asumimos Y-m-d (ya normalizado en transformRow)
            if ($name !== '' && $dob !== '') {
                $hits['full_name+birthdate'] = ($hits['full_name+birthdate'] ?? 0) + 1;
                if ($p = \App\Models\Player::where('full_name', $name)->whereDate('birthdate', $dob)->first()) {
                    return $p;
                }
            }
        }

        // 4) known_as + birthdate
        if (!empty($row['known_as']) && !empty($row['birthdate'])) {
            $alias = $norm($row['known_as']);
            $dob   = $norm($row['birthdate']);
            if ($alias !== '' && $dob !== '') {
                $hits['known_as+birthdate'] = ($hits['known_as+birthdate'] ?? 0) + 1;
                if ($p = \App\Models\Player::where('known_as', $alias)->whereDate('birthdate', $dob)->first()) {
                    return $p;
                }
            }
        }

        return null;
    }


    private function mapPosition($v): ?int
    {
        if ($v === null || $v === '') return null;
        if (is_numeric($v)) {
            $n = (int)$v;
            return in_array($n, [1,2,3,4], true) ? $n : null;
        }
        $key = strtolower(trim((string)$v));
        return self::POSITION_MAP[$key] ?? null;
    }

    private function toInt($v): ?int
    {
        if ($v === '' || $v === null) return null;
        return is_numeric($v) ? (int)$v : null;
    }

    private function toFloat($v): ?float
    {
        if ($v === '' || $v === null) return null;
        $s = (string)$v;
        $s = str_replace([' ', '.'], ['', ''], $s); // miles
        $s = str_replace(',', '.', $s);             // decimal
        return is_numeric($s) ? (float)$s : null;
    }

    private function toDate($v): ?string
    {
        if (!$v) return null;
        try { return Carbon::parse($v)->format('Y-m-d'); }
        catch (\Throwable) { return null; }
    }

    private function missingRequired(array $attrs, array $required): array
    {
        $missing = [];
        foreach ($required as $f) {
            if (!array_key_exists($f, $attrs) || $attrs[$f] === null || $attrs[$f] === '') {
                $missing[] = $f;
            }
        }
        return $missing;
    }

    private function pushErr(array &$errors, int $rowIndex, string $reason): void
    {
        $errors[] = "Fila ".($rowIndex + 2).": ".$reason; // +2 (1 header, 1 base 0)
    }
}
