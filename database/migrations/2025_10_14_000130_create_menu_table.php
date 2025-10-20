<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->string('nombre');
            $table->decimal('precio', 10, 2);
            $table->string('categoria')->nullable();
            $table->boolean('disponible')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
