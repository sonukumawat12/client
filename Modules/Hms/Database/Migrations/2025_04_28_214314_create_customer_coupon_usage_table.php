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
        Schema::create('hms_customer_coupon_usage', function (Blueprint $table) {
            $table->id(); // auto increment primary key
            $table->unsignedInteger('customer_id'); // Match customer_id as INT(10) UNSIGNED
            $table->unsignedBigInteger('coupon_id'); // Match coupon_id as BIGINT(20) UNSIGNED
            $table->timestamps();
            
            // Unique constraint to ensure a customer cannot use the same coupon more than once
            $table->unique(['customer_id', 'coupon_id']);

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('hms_coupons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hms_customer_coupon_usage');
    }
};
