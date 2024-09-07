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
        Schema::create('orderdetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('orderInfoId');
            $table->bigInteger('productId');
            $table->bigInteger('quantity');
            $table->bigInteger('value');
            $table->timestampTz('orderDate')->nullable();
            $table->timestampTz('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderdetails');
    }
};
