<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            if (!Schema::hasColumn('reminders', 'remind_at')) {
                $table->dateTime('remind_at')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            if (Schema::hasColumn('reminders', 'remind_at')) {
                $table->dropColumn('remind_at');
            }
        });
    }
};
