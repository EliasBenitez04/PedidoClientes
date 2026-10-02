<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalogo_items', function (Blueprint $table) {
            $table->id();
            $table->string('grupo', 120);
            $table->string('color', 120);
            $table->string('talle', 50);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['grupo', 'color', 'talle'], 'catalogo_grupo_color_talle_unique');
            $table->index(['grupo', 'activo']);
        });
    }

    public function down(): void { Schema::dropIfExists('catalogo_items'); }
};
