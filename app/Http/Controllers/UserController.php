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
        $request->merge([
            'username' => strtoupper(trim((string) $request->username)),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:80', 'regex:/^[A-Z0-9_]+$/', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'in:ADMIN,SUPERVISOR,LOCAL'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'rol' => $data['rol'],
            'sucursal_id' => $data['sucursal_id'],
            'activo' => true,
        ]);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        $request->merge([
            'username' => strtoupper(trim((string) $request->username)),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:80', 'regex:/^[A-Z0-9_]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'rol' => ['required', 'in:ADMIN,SUPERVISOR,LOCAL'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
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
