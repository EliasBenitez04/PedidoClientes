<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('sucursales.index', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'unique:sucursales,codigo'],
            'nombre' => ['required', 'string', 'max:120'],
        ]);

        Sucursal::create([...$data, 'activo' => true]);

        return back()->with('success', 'Sucursal creada.');
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:30', Rule::unique('sucursales', 'codigo')->ignore($sucursal->id)],
            'nombre' => ['required', 'string', 'max:120'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $sucursal->update([
            'codigo' => $data['codigo'],
            'nombre' => $data['nombre'],
            'activo' => $request->boolean('activo'),
        ]);

        return back()->with('success', 'Sucursal actualizada.');
    }
}
