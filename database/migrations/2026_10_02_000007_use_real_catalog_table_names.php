<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->dropPedidoForeignKeys();

        $this->prepareCatalog('catalogo_grupos', 'grupo', 'grupo', 120, 'grupo_id');
        $this->prepareCatalog('catalogo_colores', 'color', 'color', 120, 'color_id');
        $this->prepareCatalog('catalogo_talles', 'talle', 'talle', 50, 'talle_id');

        $this->addForeignIfValid('grupo_id', 'grupo');
        $this->addForeignIfValid('color_id', 'color');
        $this->addForeignIfValid('talle_id', 'talle');
    }

    public function down(): void
    {
        $this->dropPedidoForeignKeys();

        if (Schema::hasTable('grupo') && !Schema::hasTable('catalogo_grupos')) {
            Schema::rename('grupo', 'catalogo_grupos');
        }

        if (Schema::hasTable('color') && !Schema::hasTable('catalogo_colores')) {
            Schema::rename('color', 'catalogo_colores');
        }

        if (Schema::hasTable('talle') && !Schema::hasTable('catalogo_talles')) {
            Schema::rename('talle', 'catalogo_talles');
        }

        $this->addForeignIfValid('grupo_id', 'catalogo_grupos');
        $this->addForeignIfValid('color_id', 'catalogo_colores');
        $this->addForeignIfValid('talle_id', 'catalogo_talles');
    }

    private function prepareCatalog(
        string $oldTable,
        string $realTable,
        string $legacyValueColumn,
        int $length,
        string $pedidoForeignKey
    ): void {
        if (!Schema::hasTable($realTable)) {
            if (Schema::hasTable($oldTable)) {
                Schema::rename($oldTable, $realTable);
            } else {
                Schema::create($realTable, function (Blueprint $table) use ($length) {
                    $table->id();
                    $table->string('nombre', $length)->unique();
                    $table->boolean('activo')->default(true);
                    $table->timestamps();
                });
            }
        }

        $this->ensureColumns($realTable, $legacyValueColumn, $length);

        // Si existen ambas tablas, unificamos los datos y remapeamos pedidos existentes.
        if (Schema::hasTable($oldTable) && $oldTable !== $realTable) {
            $oldRows = DB::table($oldTable)->orderBy('id')->get();

            foreach ($oldRows as $row) {
                $nombre = trim((string) ($row->nombre ?? ''));

                if ($nombre === '') {
                    continue;
                }

                $existing = DB::table($realTable)
                    ->whereRaw('UPPER(nombre) = UPPER(?)', [$nombre])
                    ->first();

                if ($existing) {
                    $newId = $existing->id;
                } else {
                    $payload = [
                        'nombre' => $nombre,
                        'activo' => isset($row->activo) ? (bool) $row->activo : true,
                    ];

                    if (Schema::hasColumn($realTable, 'created_at')) {
                        $payload['created_at'] = now();
                    }

                    if (Schema::hasColumn($realTable, 'updated_at')) {
                        $payload['updated_at'] = now();
                    }

                    $newId = DB::table($realTable)->insertGetId($payload);
                }

                if (Schema::hasTable('pedidos') && Schema::hasColumn('pedidos', $pedidoForeignKey)) {
                    DB::table('pedidos')
                        ->where($pedidoForeignKey, $row->id)
                        ->update([$pedidoForeignKey => $newId]);
                }
            }
        }
    }

    private function ensureColumns(string $table, string $legacyValueColumn, int $length): void
    {
        if (!Schema::hasColumn($table, 'nombre')) {
            Schema::table($table, function (Blueprint $blueprint) use ($length) {
                $blueprint->string('nombre', $length)->nullable();
            });

            if (Schema::hasColumn($table, $legacyValueColumn)) {
                DB::statement(
                    'UPDATE "'.$table.'" SET nombre = "'.$legacyValueColumn.'" WHERE nombre IS NULL'
                );
            } elseif (Schema::hasColumn($table, 'descripcion')) {
                DB::statement(
                    'UPDATE "'.$table.'" SET nombre = descripcion WHERE nombre IS NULL'
                );
            }
        }

        if (!Schema::hasColumn($table, 'activo')) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->boolean('activo')->default(true);
            });
        }

        if (!Schema::hasColumn($table, 'created_at')) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasColumn($table, 'updated_at')) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->timestamp('updated_at')->nullable();
            });
        }
    }

    private function dropPedidoForeignKeys(): void
    {
        if (!Schema::hasTable('pedidos')) {
            return;
        }

        DB::statement('ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_grupo_id_foreign');
        DB::statement('ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_color_id_foreign');
        DB::statement('ALTER TABLE pedidos DROP CONSTRAINT IF EXISTS pedidos_talle_id_foreign');
    }

    private function addForeignIfValid(string $column, string $table): void
    {
        if (
            !Schema::hasTable('pedidos') ||
            !Schema::hasTable($table) ||
            !Schema::hasColumn('pedidos', $column) ||
            !Schema::hasColumn($table, 'id')
        ) {
            return;
        }

        $invalid = DB::table('pedidos')
            ->leftJoin($table, 'pedidos.'.$column, '=', $table.'.id')
            ->whereNotNull('pedidos.'.$column)
            ->whereNull($table.'.id')
            ->exists();

        if ($invalid) {
            return;
        }

        Schema::table('pedidos', function (Blueprint $blueprint) use ($column, $table) {
            $blueprint->foreign($column)->references('id')->on($table)->restrictOnDelete();
        });
    }
};
