<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('sucursal')->orderBy('name')->get();
        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'sucursales'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'in:ADMIN,SUPERVISOR,LOCAL'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
        ]);

        User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'activo' => true,
        ]);

        return back()->with('success', 'Usuario creado.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'rol' => ['required', 'in:ADMIN,SUPERVISOR,LOCAL'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'rol' => $data['rol'],
            'sucursal_id' => $data['sucursal_id'],
            'activo' => $request->boolean('activo'),
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return back()->with('success', 'Usuario actualizado.');
    }
}
