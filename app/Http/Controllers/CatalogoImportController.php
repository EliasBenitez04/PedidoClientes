<?php

namespace App\Http\Controllers;

use App\Models\CatalogoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogoImportController extends Controller
{
    public function index()
    {
        $total = CatalogoItem::count();
        $activos = CatalogoItem::where('activo', true)->count();

        return view('catalogo.importar', compact('total', 'activos'));
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'max:10240'],
        ]);

        $path = $request->file('archivo')->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return back()->withErrors(['archivo' => 'No se pudo abrir el archivo.']);
        }

        $primeraLinea = fgets($handle);
        rewind($handle);

        $delimitador = $this->detectarDelimitador($primeraLinea ?: '');
        $cabecera = fgetcsv($handle, 0, $delimitador);

        if (!$cabecera) {
            fclose($handle);
            return back()->withErrors(['archivo' => 'El archivo está vacío.']);
        }

        $cabecera = array_map(fn ($v) => $this->normalizarCabecera($v), $cabecera);
        $idxGrupo = array_search('grupo', $cabecera, true);
        $idxColor = array_search('color', $cabecera, true);
        $idxTalle = array_search('talle', $cabecera, true);

        if ($idxGrupo === false || $idxColor === false || $idxTalle === false) {
            fclose($handle);
            return back()->withErrors([
                'archivo' => 'La cabecera debe contener las columnas: grupo, color y talle.',
            ]);
        }

        $nuevos = 0;
        $actualizados = 0;
        $omitidos = 0;

        DB::transaction(function () use ($handle, $delimitador, $idxGrupo, $idxColor, $idxTalle, &$nuevos, &$actualizados, &$omitidos) {
            while (($fila = fgetcsv($handle, 0, $delimitador)) !== false) {
                $grupo = trim((string) ($fila[$idxGrupo] ?? ''));
                $color = trim((string) ($fila[$idxColor] ?? ''));
                $talle = trim((string) ($fila[$idxTalle] ?? ''));

                if ($grupo === '' || $color === '' || $talle === '') {
                    $omitidos++;
                    continue;
                }

                $existente = CatalogoItem::where('grupo', $grupo)
                    ->where('color', $color)
                    ->where('talle', $talle)
                    ->first();

                if ($existente) {
                    if (!$existente->activo) {
                        $existente->update(['activo' => true]);
                    }
                    $actualizados++;
                } else {
                    CatalogoItem::create([
                        'grupo' => $grupo,
                        'color' => $color,
                        'talle' => $talle,
                        'activo' => true,
                    ]);
                    $nuevos++;
                }
            }
        });

        fclose($handle);

        return back()->with('success', "Importación completa: {$nuevos} nuevos, {$actualizados} existentes/reactivados, {$omitidos} omitidos.");
    }

    private function detectarDelimitador(string $linea): string
    {
        $opciones = [',' => substr_count($linea, ','), ';' => substr_count($linea, ';'), "\t" => substr_count($linea, "\t")];
        arsort($opciones);
        return (string) array_key_first($opciones);
    }

    private function normalizarCabecera($valor): string
    {
        $valor = preg_replace('/^\xEF\xBB\xBF/', '', (string) $valor);
        $valor = Str::of($valor)->lower()->ascii()->replace([' ', '-', '.'], '_')->toString();

        return match ($valor) {
            'grupo_plan', 'grupo_de_plan', 'group', 'grupo' => 'grupo',
            'colour', 'colores', 'color' => 'color',
            'size', 'talles', 'talla', 'talle' => 'talle',
            default => $valor,
        };
    }
}
