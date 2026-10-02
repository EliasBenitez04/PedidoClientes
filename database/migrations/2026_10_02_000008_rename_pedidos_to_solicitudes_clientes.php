<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('pedidos') && !Schema::hasTable('solicitudes_clientes')) {
            Schema::rename('pedidos', 'solicitudes_clientes');
        }

        if (Schema::hasTable('solicitudes_clientes')) {
            DB::table('solicitudes_clientes')
                ->where('estado', 'PENDIENTE')
                ->update(['estado' => 'REGISTRADO']);

            DB::table('solicitudes_clientes')
                ->where('estado', 'PROCESADO')
                ->update(['estado' => 'REVISADO']);

            DB::table('solicitudes_clientes')
                ->where('estado', 'CANCELADO')
                ->update(['estado' => 'DESCARTADO']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('solicitudes_clientes')) {
            DB::table('solicitudes_clientes')
                ->where('estado', 'REGISTRADO')
                ->update(['estado' => 'PENDIENTE']);

            DB::table('solicitudes_clientes')
                ->where('estado', 'REVISADO')
                ->update(['estado' => 'PROCESADO']);

            DB::table('solicitudes_clientes')
                ->where('estado', 'DESCARTADO')
                ->update(['estado' => 'CANCELADO']);
        }

        if (Schema::hasTable('solicitudes_clientes') && !Schema::hasTable('pedidos')) {
            Schema::rename('solicitudes_clientes', 'pedidos');
        }
    }
};
