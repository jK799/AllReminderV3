<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Podstawowe dane przypomnienia
            $table->string('title');
            $table->text('description')->nullable();

            // Kiedy ma się odpalić
            $table->dateTime('remind_at');

            // Status
            $table->boolean('is_done')->default(false);

            // Powiązanie: Vehicle / Device / null (ogólne)
            $table->nullableMorphs('remindable'); // remindable_type, remindable_id

            $table->timestamps();

            $table->index(['user_id', 'remind_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
