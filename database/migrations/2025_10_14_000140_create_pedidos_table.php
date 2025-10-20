<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('id_pedido');
            $table->unsignedBigInteger('id_empleado');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_estado');
            $table->string('numero_mesa')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->decimal('total', 12, 2)->default(0);

            $table->foreign('id_empleado')->references('id_empleado')->on('empleados')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_estado')->references('id_estado')->on('estado_pedido')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
