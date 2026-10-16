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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            // Chave de tenant denormalizada: permite escopar por usuário
            // sem join, o que mantém o Global Scope barato.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('occurred_at');
            $table->text('chief_complaint')->nullable();
            $table->text('history')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('conduct')->nullable();
            $table->text('prescription')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'patient_id']);
            $table->index(['user_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
