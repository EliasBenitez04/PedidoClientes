<?php

namespace App\Http\Controllers;

use App\Models\CatalogoColor;
use App\Models\CatalogoGrupo;
use App\Models\CatalogoTalle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class CatalogoImportController extends Controller
{
    public function index()
    {
        $stats = [
            'grupos' => CatalogoGrupo::where('activo', true)->count(),
            'colores' => CatalogoColor::where('activo', true)->count(),
            'talles' => CatalogoTalle::where('activo', true)->count(),
        ];

        return view('catalogo.importar', compact('stats'));
    }

    public function importar(Request $request)
    {
        $data = $request->validate([
            'tipo' => ['required', 'in:grupo,color,talle'],
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
        ]);

        $config = $this->configuracion($data['tipo']);
        $archivo = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());

        try {
            $filas = in_array($extension, ['xlsx', 'xls'], true)
                ? $this->leerExcel($archivo->getRealPath())
                : $this->leerCsv($archivo->getRealPath());
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'archivo' => 'No se pudo leer el archivo. Verificá que sea un Excel/CSV válido y no esté protegido o dañado.',
            ]);
        }

        if (empty($filas)) {
            return back()->withErrors(['archivo' => 'El archivo está vacío.']);
        }

        [$filaCabecera, $indiceColumna] = $this->encontrarCabecera($filas, $config['aliases']);

        if ($filaCabecera === null || $indiceColumna === null) {
            $columnaUnica = $this->detectarColumnaUnica($filas);

            if ($columnaUnica !== null) {
                $filaCabecera = -1;
                $indiceColumna = $columnaUnica;
            } else {
                return back()->withErrors([
                    'archivo' => 'No encontré la columna '.$config['etiqueta'].'. En Excel puede estar en cualquier columna, pero la cabecera debe decir '.$config['cabecera'].' (por ejemplo: GRUPO).',
                ]);
            }
        }

        $nuevos = 0;
        $existentes = 0;
        $omitidos = 0;

        $model = $config['model'];

        foreach ($filas as $numeroFila => $fila) {
            if ($numeroFila <= $filaCabecera) {
                continue;
            }

            $valor = trim((string) ($fila[$indiceColumna] ?? ''));

            if ($valor === '') {
                $omitidos++;
                continue;
            }

            $valor = mb_strtoupper($valor, 'UTF-8');

            $registro = $model::whereRaw('UPPER(nombre) = UPPER(?)', [$valor])->first();

            if ($registro) {
                if (!$registro->activo) {
                    $registro->update(['activo' => true]);
                }

                $existentes++;
                continue;
            }

            $model::create([
                'nombre' => $valor,
                'activo' => true,
            ]);

            $nuevos++;
        }

        return back()->with(
            'success',
            $config['titulo'].' importados: '.$nuevos.' nuevos, '.$existentes.' existentes/reactivados, '.$omitidos.' omitidos.'
        );
    }

    private function leerExcel(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        // Índices numéricos desde 0 para que Excel y CSV se procesen igual.
        return $sheet->toArray(null, true, true, false);
    }

    private function leerCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw new \RuntimeException('No se pudo abrir el archivo CSV.');
        }

        $primeraLinea = fgets($handle);
        rewind($handle);

        $delimitador = $this->detectarDelimitador($primeraLinea ?: '');
        $filas = [];

        while (($fila = fgetcsv($handle, 0, $delimitador)) !== false) {
            $filas[] = $fila;
        }

        fclose($handle);

        return $filas;
    }

    private function encontrarCabecera(array $filas, array $aliases): array
    {
        // Buscamos en las primeras 25 filas por si Excel tiene títulos o espacios arriba.
        foreach (array_slice($filas, 0, 25, true) as $numeroFila => $fila) {
            foreach ($fila as $indice => $valor) {
                $normalizado = $this->normalizarCabecera($valor);

                if (in_array($normalizado, $aliases, true)) {
                    return [$numeroFila, $indice];
                }
            }
        }

        return [null, null];
    }

    private function detectarColumnaUnica(array $filas): ?int
    {
        $indices = [];

        foreach (array_slice($filas, 0, 25) as $fila) {
            foreach ($fila as $indice => $valor) {
                if (trim((string) $valor) !== '') {
                    $indices[$indice] = true;
                }
            }
        }

        return count($indices) === 1 ? (int) array_key_first($indices) : null;
    }

    private function configuracion(string $tipo): array
    {
        return match ($tipo) {
            'grupo' => [
                'model' => CatalogoGrupo::class,
                'titulo' => 'Grupos',
                'etiqueta' => 'grupo',
                'cabecera' => 'GRUPO',
                'aliases' => ['grupo', 'grupo_plan', 'grupo_de_plan', 'group'],
            ],
            'color' => [
                'model' => CatalogoColor::class,
                'titulo' => 'Colores',
                'etiqueta' => 'color',
                'cabecera' => 'COLOR',
                'aliases' => ['color', 'colores', 'colour'],
            ],
            'talle' => [
                'model' => CatalogoTalle::class,
                'titulo' => 'Talles',
                'etiqueta' => 'talle',
                'cabecera' => 'TALLE',
                'aliases' => ['talle', 'talles', 'talla', 'size'],
            ],
        };
    }

    private function detectarDelimitador(string $linea): string
    {
        $opciones = [
            ';' => substr_count($linea, ';'),
            ',' => substr_count($linea, ','),
            "\t" => substr_count($linea, "\t"),
        ];

        arsort($opciones);

        return (string) array_key_first($opciones);
    }

    private function normalizarCabecera($valor): string
    {
        $valor = preg_replace('/^\xEF\xBB\xBF/', '', (string) $valor);
        $valor = str_replace("\xC2\xA0", ' ', $valor);
        $valor = trim($valor);
        $valor = Str::ascii($valor);
        $valor = mb_strtolower($valor, 'UTF-8');
        $valor = preg_replace('/[\s\p{Z}]+/u', '_', $valor);
        $valor = str_replace(['-', '.', '/'], '_', $valor);
        $valor = preg_replace('/_+/', '_', $valor);

        return trim($valor, '_');
    }
}
