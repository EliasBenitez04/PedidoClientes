<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursales')->restrictOnDelete();
            $table->foreignId('catalogo_item_id')->constrained('catalogo_items')->restrictOnDelete();
            $table->text('observacion')->nullable();
            $table->string('estado', 20)->default('PENDIENTE');
            $table->timestamps();
            $table->index(['sucursal_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('pedidos'); }
};
