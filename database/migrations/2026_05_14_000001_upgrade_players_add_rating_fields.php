<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->decimal('rating', 5, 2)->default(3.50)->after('dupr');
            $table->unsignedInteger('wins')->default(0)->after('rating');
            $table->unsignedInteger('losses')->default(0)->after('wins');
            $table->unsignedInteger('matches_played')->default(0)->after('losses');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['rating', 'wins', 'losses', 'matches_played']);
        });
    }
};
