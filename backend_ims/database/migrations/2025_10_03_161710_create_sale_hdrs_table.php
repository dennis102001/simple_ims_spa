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
        Schema::create('sale_hdrs', function (Blueprint $table) {
            $table->id();
            $table->string('so_number');
            $table->integer('created_by');
            $table->string('remarks')->nullable();
            $table->string('status');
            $table->integer('customer_id')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->integer('confirmed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_hdrs');
    }
};
