<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('solicitudes_clientes', function (Blueprint $table) {
            $table->foreignId('revisado_por_id')
                ->nullable()
                ->after('estado')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('revisado_en')
                ->nullable()
                ->after('revisado_por_id');

            $table->index(['estado', 'revisado_en']);
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_clientes', function (Blueprint $table) {
            $table->dropIndex(['estado', 'revisado_en']);
            $table->dropForeign(['revisado_por_id']);
            $table->dropColumn(['revisado_por_id', 'revisado_en']);
        });
    }
};
