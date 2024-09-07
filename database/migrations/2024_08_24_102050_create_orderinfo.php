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
        Schema::create('orderinfo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tenantId');
            $table->bigInteger('customerId');
            $table->bigInteger('deliveryManId');
            $table->timestampTz('orderedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('ip', 100)->nullable();
            $table->string('geo', 100)->nullable();
            $table->bigInteger('amount');
            $table->bigInteger('taxamount');
            $table->string('orderedby', 255)->nullable();
            $table->timestampTz('deliveyDate')->nullable();
            $table->timestampTz('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderinfo');
    }
};
