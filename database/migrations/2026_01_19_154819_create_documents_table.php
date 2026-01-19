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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
            $table->string('title')->nullable();     // np. "Polisa OC 2026"
            $table->text('notes')->nullable();
        
            // plik
            $table->string('original_name');
            $table->string('path');                  // np. documents/abc.pdf
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
        
            $table->timestamps();
        
            $table->index(['user_id', 'created_at']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
