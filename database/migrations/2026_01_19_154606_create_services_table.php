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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
            // przypisanie: device / vehicle / none
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
        
            $table->string('title');                 // np. "Wymiana oleju"
            $table->text('description')->nullable();
        
            // harmonogram
            $table->date('last_done_at')->nullable(); // kiedy ostatnio wykonano
            $table->date('next_due_at')->nullable();  // kiedy ma być następny raz
        
            // cykliczność (prosta i wystarczająca)
            $table->unsignedSmallInteger('interval_value')->nullable(); // np. 6
            $table->enum('interval_unit', ['days', 'weeks', 'months', 'years'])->nullable(); // np. months
        
            $table->boolean('is_active')->default(true);
        
            $table->timestamps();
        
            $table->index(['user_id', 'next_due_at']);
            $table->index(['user_id', 'is_active']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
