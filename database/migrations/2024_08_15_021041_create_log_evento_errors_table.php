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
        Schema::create('log_evento_error', function (Blueprint $table) {
            $table->id();
            $table->string("id_log");
            $table->string("id_evento");
            $table->string("id_entidad")->nullable();
            $table->string('notes',2000);
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_eventos_errors');
    }
};
