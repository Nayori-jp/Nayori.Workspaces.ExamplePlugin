<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('example_plugin_records', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('active');
            $table->text('summary')->nullable();
            $table->timestamps();
        });

        DB::table('example_plugin_records')->insert([
            [
                'name' => 'Shared CRUD metadata',
                'slug' => 'shared-crud-metadata',
                'status' => 'active',
                'summary' => 'Shows how a plugin list page can be defined through one index metadata object.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Reusable list renderer',
                'slug' => 'reusable-list-renderer',
                'status' => 'active',
                'summary' => 'Demonstrates shared toolbar, columns, actions, and empty-state rendering.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Detail page example',
                'slug' => 'detail-page-example',
                'status' => 'inactive',
                'summary' => 'Shows how plugins can reuse shared detail headers and stat cards too.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('example_plugin_records');
    }
};
