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
        Schema::create('tenant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 255)->nullable();
            $table->string('name', 255);
            $table->string('address', 255);
            $table->bigInteger('pincode');
            $table->enum('status', [1, 0])->default(1);
            $table->string('logoUrl', 100)->nullable();
            $table->bigInteger('supportNumber');
            $table->string('socialLinks', 255)->nullable();
            $table->string('createdUser', 255)->nullable();
            $table->string('updatedUser', 255)->nullable();
            $table->timestampTz('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant');
    }
};
