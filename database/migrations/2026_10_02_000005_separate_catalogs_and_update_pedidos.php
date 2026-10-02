<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalogo_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('catalogo_colores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('catalogo_talles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('grupo_id')->nullable()->after('sucursal_id')->constrained('catalogo_grupos')->restrictOnDelete();
            $table->foreignId('color_id')->nullable()->after('grupo_id')->constrained('catalogo_colores')->restrictOnDelete();
            $table->foreignId('talle_id')->nullable()->after('color_id')->constrained('catalogo_talles')->restrictOnDelete();
        });

        // Migrar automáticamente los valores del catálogo combinado anterior.
        DB::table('catalogo_items')
            ->select('grupo')
            ->whereNotNull('grupo')
            ->where('grupo', '<>', '')
            ->distinct()
            ->orderBy('grupo')
            ->get()
            ->each(function ($row) {
                DB::table('catalogo_grupos')->updateOrInsert(
                    ['nombre' => trim($row->grupo)],
                    ['activo' => true, 'updated_at' => now(), 'created_at' => now()]
                );
            });

        DB::table('catalogo_items')
            ->select('color')
            ->whereNotNull('color')
            ->where('color', '<>', '')
            ->distinct()
            ->orderBy('color')
            ->get()
            ->each(function ($row) {
                DB::table('catalogo_colores')->updateOrInsert(
                    ['nombre' => trim($row->color)],
                    ['activo' => true, 'updated_at' => now(), 'created_at' => now()]
                );
            });

        DB::table('catalogo_items')
            ->select('talle')
            ->whereNotNull('talle')
            ->where('talle', '<>', '')
            ->distinct()
            ->orderBy('talle')
            ->get()
            ->each(function ($row) {
                DB::table('catalogo_talles')->updateOrInsert(
                    ['nombre' => trim($row->talle)],
                    ['activo' => true, 'updated_at' => now(), 'created_at' => now()]
                );
            });

        // Completar las nuevas referencias de pedidos históricos.
        DB::statement("
            UPDATE pedidos p
               SET grupo_id = g.id
              FROM catalogo_items ci
              JOIN catalogo_grupos g ON g.nombre = ci.grupo
             WHERE p.catalogo_item_id = ci.id
               AND p.grupo_id IS NULL
        ");

        DB::statement("
            UPDATE pedidos p
               SET color_id = c.id
              FROM catalogo_items ci
              JOIN catalogo_colores c ON c.nombre = ci.color
             WHERE p.catalogo_item_id = ci.id
               AND p.color_id IS NULL
        ");

        DB::statement("
            UPDATE pedidos p
               SET talle_id = t.id
              FROM catalogo_items ci
              JOIN catalogo_talles t ON t.nombre = ci.talle
             WHERE p.catalogo_item_id = ci.id
               AND p.talle_id IS NULL
        ");

        // El catálogo combinado queda solo para compatibilidad histórica.
        DB::statement('ALTER TABLE pedidos ALTER COLUMN catalogo_item_id DROP NOT NULL');

        Schema::table('pedidos', function (Blueprint $table) {
            $table->index(['grupo_id', 'created_at']);
            $table->index(['color_id', 'created_at']);
            $table->index(['talle_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['grupo_id', 'created_at']);
            $table->dropIndex(['color_id', 'created_at']);
            $table->dropIndex(['talle_id', 'created_at']);

            $table->dropForeign(['grupo_id']);
            $table->dropForeign(['color_id']);
            $table->dropForeign(['talle_id']);

            $table->dropColumn(['grupo_id', 'color_id', 'talle_id']);
        });

        Schema::dropIfExists('catalogo_talles');
        Schema::dropIfExists('catalogo_colores');
        Schema::dropIfExists('catalogo_grupos');
    }
};
