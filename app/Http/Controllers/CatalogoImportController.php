<?php

namespace App\Http\Controllers;

use App\Models\CatalogoColor;
use App\Models\CatalogoGrupo;
use App\Models\CatalogoTalle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'archivo' => ['required', 'file', 'max:10240'],
        ]);

        $config = $this->configuracion($data['tipo']);
        $path = $request->file('archivo')->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return back()->withErrors(['archivo' => 'No se pudo abrir el archivo.']);
        }

        $primeraLinea = fgets($handle);
        rewind($handle);

        $delimitador = $this->detectarDelimitador($primeraLinea ?: '');
        $primeraFila = fgetcsv($handle, 0, $delimitador);

        if (!$primeraFila) {
            fclose($handle);
            return back()->withErrors(['archivo' => 'El archivo está vacío.']);
        }

        $cabecera = array_map(fn ($v) => $this->normalizarCabecera($v), $primeraFila);
        $indice = $this->buscarIndice($cabecera, $config['aliases']);
        $primeraEsCabecera = $indice !== false;

        // Si es un archivo de una sola columna sin cabecera, usamos la primera columna.
        if ($indice === false && count($primeraFila) === 1) {
            $indice = 0;
        }

        if ($indice === false) {
            fclose($handle);
            return back()->withErrors([
                'archivo' => 'No encontré la columna '.$config['etiqueta'].'. Usá un archivo con una columna llamada '.$config['cabecera'].'.',
            ]);
        }

        $nuevos = 0;
        $existentes = 0;
        $omitidos = 0;

        $procesar = function (array $fila) use ($indice, $config, &$nuevos, &$existentes, &$omitidos) {
            $valor = trim((string) ($fila[$indice] ?? ''));

            if ($valor === '') {
                $omitidos++;
                return;
            }

            $valor = mb_strtoupper($valor, 'UTF-8');
            $model = $config['model'];

            $registro = $model::whereRaw('UPPER(nombre) = ?', [$valor])->first();

            if ($registro) {
                if (!$registro->activo) {
                    $registro->update(['activo' => true]);
                }
                $existentes++;
                return;
            }

            $model::create([
                'nombre' => $valor,
                'activo' => true,
            ]);

            $nuevos++;
        };

        if (!$primeraEsCabecera) {
            $procesar($primeraFila);
        }

        while (($fila = fgetcsv($handle, 0, $delimitador)) !== false) {
            $procesar($fila);
        }

        fclose($handle);

        return back()->with(
            'success',
            $config['titulo'].' importados: '.$nuevos.' nuevos, '.$existentes.' existentes/reactivados, '.$omitidos.' omitidos.'
        );
    }

    private function configuracion(string $tipo): array
    {
        return match ($tipo) {
            'grupo' => [
                'model' => CatalogoGrupo::class,
                'titulo' => 'Grupos',
                'etiqueta' => 'grupo',
                'cabecera' => 'grupo',
                'aliases' => ['grupo', 'grupo_plan', 'grupo_de_plan', 'group'],
            ],
            'color' => [
                'model' => CatalogoColor::class,
                'titulo' => 'Colores',
                'etiqueta' => 'color',
                'cabecera' => 'color',
                'aliases' => ['color', 'colores', 'colour'],
            ],
            'talle' => [
                'model' => CatalogoTalle::class,
                'titulo' => 'Talles',
                'etiqueta' => 'talle',
                'cabecera' => 'talle',
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

        return Str::of($valor)
            ->lower()
            ->ascii()
            ->replace([' ', '-', '.'], '_')
            ->trim()
            ->toString();
    }

    private function buscarIndice(array $cabecera, array $aliases)
    {
        foreach ($aliases as $alias) {
            $indice = array_search($alias, $cabecera, true);
            if ($indice !== false) {
                return $indice;
            }
        }

        return false;
    }
}
