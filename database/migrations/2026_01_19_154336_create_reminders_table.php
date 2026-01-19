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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
            // przypisanie: albo device, albo vehicle, albo null/null (ogólne)
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
        
            $table->string('title');
            $table->text('description')->nullable();
        
            $table->dateTime('due_at')->nullable();           // kiedy ma się pojawić przypomnienie
            $table->dateTime('completed_at')->nullable();     // kiedy użytkownik oznaczy jako zrobione
        
            $table->boolean('is_active')->default(true);
        
            $table->timestamps();
        
            // indeksy pod filtrowanie
            $table->index(['user_id', 'due_at']);
            $table->index(['user_id', 'is_active']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
