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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tenantId');
            $table->string('name', 255);
            $table->string('role', 255)->nullable();
            $table->enum('status', [1, 0])->default(1);
            $table->timestampTz('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
