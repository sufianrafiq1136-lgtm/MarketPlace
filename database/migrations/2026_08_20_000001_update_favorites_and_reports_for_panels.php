<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->unique(['user_id', 'ad_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->string('status')->default('open')->after('reason');
            $table->text('admin_notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'ad_id']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_notes']);
        });
    }
};
