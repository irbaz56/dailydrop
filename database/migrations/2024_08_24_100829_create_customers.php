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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tenantId');
            $table->string('name', 255);
            $table->bigInteger('mobile');
            $table->string('address1', 255);
            $table->string('address2', 255)->nullable();
            $table->bigInteger('pincode');
            $table->string('landMark', 255)->nullable();
            $table->string('geo', 100)->nullable();
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
        Schema::dropIfExists('customers');
    }
};
