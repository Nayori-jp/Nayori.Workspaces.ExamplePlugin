<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('example_plugin_records', 'business_id')) {
            return;
        }

        Schema::table('example_plugin_records', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained('businesses')
                ->cascadeOnDelete();
            $table->unique(['business_id', 'slug']);
        });

        $firstBusinessId = DB::table('businesses')->orderBy('id')->value('id');

        if ($firstBusinessId !== null) {
            DB::table('example_plugin_records')
                ->whereNull('business_id')
                ->update(['business_id' => $firstBusinessId]);
        } else {
            DB::table('example_plugin_records')->whereNull('business_id')->delete();
        }
    }

    public function down(): void
    {
        // This compatibility migration may be a no-op on fresh installations,
        // where the original create-table migration already owns the column.
        // Reversing it would therefore make fresh rollbacks order-dependent.
    }
};
