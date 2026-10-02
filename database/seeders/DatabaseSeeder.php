<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $central = Sucursal::firstOrCreate(
            ['codigo' => 'CC'],
            ['nombre' => 'CASA CENTRAL', 'activo' => true]
        );

        $username = strtoupper(env('ADMIN_USERNAME', 'ADMIN'));

        User::updateOrCreate(
            ['username' => $username],
            [
                'name' => env('ADMIN_NAME', 'Administrador'),
                'email' => env('ADMIN_EMAIL') ?: null,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Cambiar123!')),
                'rol' => 'ADMIN',
                'sucursal_id' => $central->id,
                'activo' => true,
            ]
        );
    }
}
