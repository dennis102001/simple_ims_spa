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
        Schema::create('purchase_order_hdrs', function (Blueprint $table) {
            $table->id();
            $table->string('po_number');
            $table->integer('created_by');
            $table->string('remarks')->nullable();
            $table->string('status');
            $table->integer('supplier_id')->nullable();
            $table->date('received_date')->nullable();
            $table->integer('received_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_hdrs');
    }
};
