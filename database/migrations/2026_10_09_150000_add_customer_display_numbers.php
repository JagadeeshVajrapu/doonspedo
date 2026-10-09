<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'display_no')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('display_no')->nullable()->unique()->after('id');
            });
        }

        $number = 1;
        foreach (DB::table('users')->orderBy('id')->pluck('id') as $id) {
            DB::table('users')->where('id', $id)->whereNull('display_no')->update([
                'display_no' => $number++,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'display_no')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['display_no']);
                $table->dropColumn('display_no');
            });
        }
    }
};
