<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baile_reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('baile_usuarios')->cascadeOnDelete();
            $table->foreignId('id_lugar')->constrained('baile_lugares')->cascadeOnDelete();
            $table->boolean('pago')->default(false)->index();
            $table->string('payment_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamps();

            $table->unique('id_lugar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baile_reservas');
    }
};
