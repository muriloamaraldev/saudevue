<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // Chave de tenant: delimita o espaço de trabalho do profissional.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            // CPF sem máscara, 11 dígitos. O normalizador grava só números.
            $table->string('cpf', 11);
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // O mesmo CPF pode ser paciente de outro profissional — o
            // isolamento é por tenant, não global.
            $table->unique(['user_id', 'cpf']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
